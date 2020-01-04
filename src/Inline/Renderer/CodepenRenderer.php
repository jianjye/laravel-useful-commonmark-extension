<?php

namespace JohnnyHuy\Laravel\Inline\Renderer;

use ErrorException;
use JohnnyHuy\Laravel\Inline\Element\Codepen;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\AbstractWebResource;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\Configuration;

class CodepenRenderer implements InlineRendererInterface, GetContentInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @param AbstractInline|AbstractWebResource $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement|string
     * @throws ErrorException
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Codepen)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        // Use a oEmbed route to get codepen details
        $apiUrl = "https://codepen.io/api/oembed?url={$inline->getUrl()}&format=json";
        $apiResponse = $this->getContent($apiUrl);

        //seems that the used codepen url is invalid
        //or codepen is currently not available
        if (is_null($apiResponse)) {
            throw new ErrorException('Codepen request returned null: ' . $apiUrl);
        }

        //parse the oembed response
        $embed = json_decode($apiResponse);

        //return the oembed html snippet with a div as wrapper element
        return new HtmlElement('div', ['class' => 'codepen-container'], $embed->html);
    }

    /**
     * @param string $url
     * @return string
     */
    public function getContent(string $url): string
    {
        return file_get_contents($url);
    }
}
