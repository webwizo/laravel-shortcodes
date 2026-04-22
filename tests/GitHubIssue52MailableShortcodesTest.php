<?php

namespace Webwizo\Shortcodes;

use Illuminate\Mail\Mailable;

/**
 * GitHub #52 — allow enabling shortcodes for mailable views.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/issues/52
 */
class GitHubIssue52MailableShortcodesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        view()->addNamespace('Gh52', __DIR__ . '/views');
    }

    public function test_mailable_can_enable_shortcode_compilation_explicitly(): void
    {
        $this->shortcode->register('b', function ($shortcode, $content) {
            return sprintf('<strong class="%s">%s</strong>', $shortcode->class, $content);
        });

        $mailable = new class extends Mailable
        {
            public function build()
            {
                return $this
                    ->view('Gh52::issue52-mail', ['content' => 'Mail content'])
                    ->withShortcodes();
            }
        };

        $html = $mailable->render();

        $this->assertStringContainsString('<strong class="mail">Mail content</strong>', $html);
        $this->assertStringNotContainsString('__laravel_shortcodes_enable_for_render', $html);
    }
}
