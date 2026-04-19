<?php

namespace Webwizo\Shortcodes;

/**
 * GitHub #51 — shortcode as the first content in a rendered view must not break attribute handling.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/issues/51
 */
class GitHubIssue51ShortcodeAtBeginningTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        view()->addNamespace('Gh51', __DIR__ . '/views');
    }

    public function test_blade_whose_first_line_starts_with_self_closing_shortcode_renders_attributes(): void
    {
        $this->shortcode->register('library', function ($shortcode) {
            return 'TYPE:' . (string) $shortcode->type;
        });

        $html = view('Gh51::issue51-start')->withShortcodes()->render();

        $this->assertStringContainsString('TYPE:document', $html);
    }
}
