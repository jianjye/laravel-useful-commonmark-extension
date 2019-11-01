<?php

declare(strict_types=1);

namespace JohnnyHuy\Laravel\Inline\Parser;

use JohnnyHuy\Laravel\Inline\Element\SoundCloud;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

class SoundCloudParser implements InlineParserInterface
{
    /**
     * @param InlineParserContext $inlineContext
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $savedState = $cursor->saveState();

        $cursor->advance();

        // Check that the given user input is a valid soundcloud url
        // and the required `soundcloud:` or `:sc` prefix exists
        $regex = '/^(?:soundcloud|sc)\s((?:https?\:\/\/)?(?:www\.)?(?:soundcloud\.com\/)[^&#\s\?]+\/[^&#\s\?]+)/';
        $validate = $cursor->match($regex);

        // The computer says no
        if (!$validate) {
            $cursor->restoreState($savedState);

            return false;
        }

        $matches = [];
        preg_match($regex, $validate, $matches);

        $inlineContext->getContainer()->appendChild(new SoundCloud($matches[1]));

        return true;
    }

    /**
     * @return string[]
     */
    public function getCharacters(): array
    {
        return [':'];
    }
}
