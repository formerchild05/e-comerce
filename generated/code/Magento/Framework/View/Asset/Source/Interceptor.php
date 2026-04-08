<?php
namespace Magento\Framework\View\Asset\Source;

/**
 * Interceptor class for @see \Magento\Framework\View\Asset\Source
 */
class Interceptor extends \Magento\Framework\View\Asset\Source implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Filesystem $filesystem, \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory, \Magento\Framework\View\Asset\PreProcessor\Pool $preProcessorPool, \Magento\Framework\View\Design\FileResolution\Fallback\StaticFile $fallback, \Magento\Framework\View\Design\Theme\ListInterface $themeList, \Magento\Framework\View\Asset\PreProcessor\ChainFactoryInterface $chainFactory)
    {
        $this->___init();
        parent::__construct($filesystem, $readFactory, $preProcessorPool, $fallback, $themeList, $chainFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFile');
        return $pluginInfo ? $this->___callPlugins('getFile', func_get_args(), $pluginInfo) : parent::getFile($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getContent');
        return $pluginInfo ? $this->___callPlugins('getContent', func_get_args(), $pluginInfo) : parent::getContent($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContentType(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSourceContentType');
        return $pluginInfo ? $this->___callPlugins('getSourceContentType', func_get_args(), $pluginInfo) : parent::getSourceContentType($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function findSource(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'findSource');
        return $pluginInfo ? $this->___callPlugins('findSource', func_get_args(), $pluginInfo) : parent::findSource($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getContentType');
        return $pluginInfo ? $this->___callPlugins('getContentType', func_get_args(), $pluginInfo) : parent::getContentType($path);
    }

    /**
     * {@inheritdoc}
     */
    public function findRelativeSourceFilePath(\Magento\Framework\View\Asset\LocalInterface $asset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'findRelativeSourceFilePath');
        return $pluginInfo ? $this->___callPlugins('findRelativeSourceFilePath', func_get_args(), $pluginInfo) : parent::findRelativeSourceFilePath($asset);
    }
}
