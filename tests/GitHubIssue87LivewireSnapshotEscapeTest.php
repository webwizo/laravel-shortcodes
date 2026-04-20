<?php

namespace Webwizo\Shortcodes;

/**
 * GitHub #87 — escaped shortcode handling must not alter non-shortcode `[[` sequences.
 *
 * @see https://github.com/webwizo/laravel-shortcodes/issues/87
 */
class GitHubIssue87LivewireSnapshotEscapeTest extends TestCase
{
    public function test_double_brackets_inside_snapshot_like_json_are_not_rewritten(): void
    {
        $this->shortcode->register('demo', fn () => 'demo');

        $input = '<div wire:snapshot="{&quot;data&quot;:{&quot;items&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}]}}">[[demo]]</div>';
        $output = $this->shortcode->compile($input);

        $this->assertStringContainsString(
            '{&quot;data&quot;:{&quot;items&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}]}}',
            $output
        );
        $this->assertStringContainsString('<div wire:snapshot=', $output);
        $this->assertStringContainsString('[demo]', $output, 'Escaped shortcode syntax should still unescape normally.');
    }
}
