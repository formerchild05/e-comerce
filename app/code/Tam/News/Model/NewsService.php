<?php

namespace Tam\News\Model;

use Magento\Framework\HTTP\Client\Curl;
use RuntimeException;

class NewsService
{
    private const SOURCE_URL = 'https://vnexpress.net/rss/kinh-doanh.rss';

    private Curl $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function getFeedData(): array
    {
        $this->curl->setOption(CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $this->curl->setOption(CURLOPT_TIMEOUT, 15);
        $this->curl->setOption(CURLOPT_CONNECTTIMEOUT, 8);
        $this->curl->setOption(CURLOPT_FOLLOWLOCATION, true);

        $this->curl->get(self::SOURCE_URL);
        $response = trim((string) $this->curl->getBody());
        $status = $this->curl->getStatus();

        if ($status !== 200) {
            throw new RuntimeException('VnExpress RSS error (HTTP ' . $status . ')');
        }

        if ($response === '') {
            throw new RuntimeException('VnExpress RSS returned empty response');
        }

        $rss = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($rss === false || !isset($rss->channel)) {
            throw new RuntimeException('VnExpress RSS returned invalid XML');
        }

        $channel = $rss->channel;
        $items = [];
        $count = 0;

        foreach ($channel->item as $item) {
            $rawDescription = (string) ($item->description ?? '');
            $cleanDescription = trim(html_entity_decode(strip_tags($rawDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            $imageUrl = (string) ($item->enclosure['url'] ?? '');
            if ($imageUrl === '' && preg_match('/<img[^>]*src=["\']([^"\']+)["\']/i', $rawDescription, $matches)) {
                $imageUrl = $matches[1];
            }

            $items[] = [
                'title' => trim((string) ($item->title ?? '')),
                'link' => trim((string) ($item->link ?? '')),
                'pub_date' => trim((string) ($item->pubDate ?? '')),
                'description' => $cleanDescription,
                'image_url' => trim($imageUrl),
            ];

            $count++;
            if ($count >= 20) {
                break;
            }
        }

        return [
            'title' => trim((string) ($channel->title ?? 'Tin tức')),
            'pub_date' => trim((string) ($channel->pubDate ?? '')),
            'source_url' => self::SOURCE_URL,
            'items' => $items,
        ];
    }
}
