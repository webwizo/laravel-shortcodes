<?php

namespace Webwizo\Shortcodes;

/**
 * GitHub #37 — prose that contains `[` must not be eaten when it is not a registered shortcode.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/issues/37
 */
class GitHubIssue37MalformedBracketsTest extends TestCase
{
    public function test_square_bracket_text_that_does_not_match_a_registered_tag_is_left_unchanged(): void
    {
        $this->shortcode->register('gallery', function () {
            return 'G';
        });

        $input = "THANK YOU VERY MUCH!\n[I AM SO SORRY THAT MY ENGLISH IS POOR ....\n<em class=\"fab fa-youtube\"></em>\n";

        $this->assertSame($input, $this->shortcode->compile($input));
    }

    public function test_opening_bracket_followed_by_space_is_not_treated_as_shortcode(): void
    {
        $this->shortcode->register('b', fn ($_, $c) => '<b>' . $c . '</b>');

        $input = 'Text [ not a shortcode ] more';

        $this->assertSame($input, $this->shortcode->compile($input));
    }
}
