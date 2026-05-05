<?php

namespace Tam\News\Block;

use Magento\Framework\View\Element\Template;
use Tam\News\Model\NewsService;

class News extends Template
{
    private NewsService $newsService;

    public function __construct(
        Template\Context $context,
        NewsService $newsService,
        array $data = []
    ) {
        $this->newsService = $newsService;
        parent::__construct($context, $data);
    }

    public function getNewsData(): array
    {
        try {
            return $this->newsService->getFeedData();
        } catch (\Throwable $e) {
            return [
                'title' => 'Tin tức',
                'pub_date' => '',
                'source_url' => '',
                'items' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getLatestItems(int $limit = 5): array
    {
        $data = $this->getNewsData();
        $items = $data['items'] ?? [];

        if ($limit <= 0) {
            return [];
        }

        return array_slice($items, 0, $limit);
    }

    public function getNewsPageUrl(): string
    {
        return $this->getUrl('news');
    }
}
