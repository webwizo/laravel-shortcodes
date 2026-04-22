<?php
namespace Webwizo\Shortcodes\Compilers;

use Illuminate\Support\Str;

class ShortcodeCompiler
{

    /**
     * Enabled state
     *
     * @var boolean
     */
    protected $enabled = false;

    /**
     * Enable strip state
     *
     * @var boolean
     */
    protected $strip = false;

    /**
     * @var
     */
    protected $matches;

    /**
     * Registered laravel-shortcodes
     *
     * @var array
     */
    protected $registered = [];

    /**
     * Attached View Data
     *
     * @var array
     */
    protected $data = [];

    protected $_viewData;

    /**
     * Enable
     *
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Determine whether shortcode compilation is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Add a new shortcode
     *
     * @param string          $name
     * @param callable|string $callback
     */
    public function add($name, $callback)
    {
        $this->registered[$name] = $callback;
    }

    public function attachData($data)
    {
        $this->data = $data;
    }

    /**
     * Compile the contents
     *
     * @param  string $value
     *
     * @return string
     */
    public function compile($value)
    {
        // Only continue is laravel-shortcodes have been registered
        if (!$this->enabled || !$this->hasShortcodes()) {
            return $value;
        }
        // Set empty result
        $result = '';
        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        return $result;
    }

    /**
     * Check if laravel-shortcodes have been registered
     *
     * @return boolean
     */
    public function hasShortcodes()
    {
        return !empty($this->registered);
    }

    /**
     * Parse the tokens from the template.
     *
     * @param  array $token
     *
     * @return string
     */
    protected function parseToken($token)
    {
        list($id, $content) = $token;
        if ($id == T_INLINE_HTML) {
            $content = $this->renderShortcodes($content);
        }

        return $content;
    }

    /**
     * Render laravel-shortcodes
     *
     * @param  string $value
     *
     * @return string
     */
    protected function renderShortcodes($value)
    {
        if (!$this->hasShortcodes()) {
            return $value;
        }

        return $this->replaceShortcodesInString($value, [$this, 'render']);
    }

    // get view data
    public function viewData($viewData)
    {
        $this->_viewData = $viewData;
        return $this;
    }

    /**
     * Render the current calld shortcode.
     *
     * @param  array $matches
     *
     * @return string
     */
    public function render($matches)
    {
        // Compile the shortcode
        $compiled = $this->compileShortcode($matches);
        $name = $compiled->getName();
        $viewData = $this->_viewData;

        // Render the shortcode through the callback
        return call_user_func_array($this->getCallback($name), [
            $compiled,
            $compiled->getContent(),
            $this,
            $name,
            $viewData
        ]);
    }

    /**
     * Get Compiled Attributes.
     *
     * @param $matches
     *
     * @return \Webwizo\Shortcodes\Shortcode
     */
    protected function compileShortcode($matches)
    {
        // Set matches
        $this->setMatches($matches);
        // pars the attributes
        $attributes = $this->parseAttributes($this->matches[3]);

        // return shortcode instance
        return new Shortcode(
            $this->getName(),
            $this->getContent(),
            $attributes
        );
    }

    /**
     * Set the matches
     *
     * @param array $matches
     */
    protected function setMatches($matches = [])
    {
        $this->matches = $matches;
    }

    /**
     * Return the shortcode name
     *
     * @return string
     */
    public function getName()
    {
        return $this->matches[2];
    }

    /**
     * Return the shortcode content
     *
     * @return string
     */
    public function getContent()
    {
        // Compile the content, to support nested laravel-shortcodes
        return $this->compile($this->matches[5]);
    }

    /**
     * Return the view data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Get the callback for the current shortcode (class or callback)
     *
     * @param  string $name
     *
     * @return callable|array
     */
    public function getCallback($name)
    {
        // Get the callback from the laravel-shortcodes array
        $callback = $this->registered[$name];
        // if is a string
        if (is_string($callback)) {
            // Parse the callback
            list($class, $method) = Str::parseCallback($callback, 'register');
            // If the class exist
            if (class_exists($class)) {
                // return class and method
                return [
                    app($class),
                    $method
                ];
            }
        }

        return $callback;
    }

    /**
     * Parse the shortcode attributes
     *
     * @author Wordpress
     * @return array
     */
    protected function parseAttributes($text)
    {
        // decode attribute values
        $text = htmlspecialchars_decode($text, ENT_QUOTES);

        $attributes = [];
        // Match WordPress-style attributes; allow `]` after a value (issue #56) and hyphenated keys (WP 4.4+).
        $pattern = '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|\]|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|\]|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|\]|$)|"([^"]*)"(?:\s|\]|$)|\'([^\']*)\'(?:\s|\]|$)|(\S+)(?:\s|\]|$)/';
        // Match
        if (preg_match_all($pattern, preg_replace('/[\x{00a0}\x{200b}]+/u', " ", $text), $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $attributes[strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $attributes[strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $attributes[strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) && strlen($m[7])) {
                    $attributes[] = stripcslashes($m[7]);
                } elseif (isset($m[8]) && strlen($m[8])) {
                    $attributes[] = stripcslashes($m[8]);
                } elseif (isset($m[9])) {
                    $attributes[] = stripcslashes($m[9]);
                }
            }
        } else {
            $attributes = ltrim($text);
        }

        // return attributes
        return is_array($attributes) ? $attributes : [$attributes];
    }

    /**
     * Get shortcode names
     *
     * @return string
     */
    protected function getShortcodeNames()
    {
        $names = array_keys($this->registered);
        usort($names, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });

        return implode('|', array_map('preg_quote', $names));
    }

    /**
     * Get shortcode regex.
     *
     * @author Wordpress
     * @return string
     */
    protected function getRegex()
    {
        $shortcodeNames = $this->getShortcodeNames();

        return "\\[(\\[?)($shortcodeNames)(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)";
    }

    /**
     * Remove all shortcode tags from the given content.
     *
     * @param string $content Content to remove shortcode tags.
     *
     * @return string Content without shortcode tags.
     */
    public function strip($content)
    {
        if (empty($this->registered)) {
            return $content;
        }

        return $this->replaceShortcodesInString($content, [$this, 'stripTag']);
    }

    /**
     * @return boolean
     */
    public function getStrip()
    {
        return $this->strip;
    }

    /**
     * @param boolean $strip
     */
    public function setStrip($strip)
    {
        $this->strip = $strip;
    }

    /**
     * Remove shortcode tag
     *
     * @param type $m
     *
     * @return string Content without shortcode tag.
     */
    protected function stripTag($m)
    {
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        return $m[1] . $m[6];
    }

    /**
     * Replace shortcodes left-to-right using balanced matching for nested tags with the same name (GitHub #59).
     *
     * @param  callable(array): string  $callback
     */
    protected function replaceShortcodesInString(string $value, callable $callback): string
    {
        $namesPattern = $this->getShortcodeNames();
        if ($namesPattern === '') {
            return $value;
        }

        $out = '';
        $offset = 0;
        $len = strlen($value);

        while ($offset < $len) {
            if (!preg_match('/\[\[|\[(' . $namesPattern . ')(?![\w-])/s', $value, $m, PREG_OFFSET_CAPTURE, $offset)) {
                $out .= substr($value, $offset);
                break;
            }

            $matchStart = (int) $m[0][1];
            $token = $m[0][0];

            if ($token === '[[') {
                $out .= substr($value, $offset, $matchStart - $offset);
                $escapedOpen = preg_match(
                    '/\[\[(' . $namesPattern . ')(?![\w-])/i',
                    $value,
                    $escapedMatch,
                    PREG_OFFSET_CAPTURE,
                    $matchStart
                );

                if ($escapedOpen && (int) $escapedMatch[0][1] === $matchStart) {
                    // Handle WordPress-style escaped shortcode tags only (e.g. [[tag]]).
                    // Leave non-shortcode double brackets untouched to avoid corrupting JSON like Livewire snapshots.
                    $escapedOpenEnd = $this->findEndOfOpeningShortcodeTag($value, $matchStart + 1);
                    if ($escapedOpenEnd !== null && $escapedOpenEnd < $len && $value[$escapedOpenEnd] === ']') {
                        $out .= substr($value, $matchStart + 1, $escapedOpenEnd - $matchStart);
                        $offset = $escapedOpenEnd + 1;

                        continue;
                    }
                }

                $out .= '[[';
                $offset = $matchStart + 2;

                continue;
            }

            $out .= substr($value, $offset, $matchStart - $offset);

            $name = $m[1][0];
            $openBracket = $matchStart;

            $openEnd = $this->findEndOfOpeningShortcodeTag($value, $openBracket);
            if ($openEnd === null) {
                $out .= '[';
                $offset = $openBracket + 1;

                continue;
            }

            $matchArr = $this->composeShortcodeMatch($value, $openBracket, $openEnd, $name);
            if ($matchArr === null) {
                $out .= '[';
                $offset = $openBracket + 1;

                continue;
            }

            $out .= $callback($matchArr);
            $offset = $matchArr['_end'];
        }

        return $out;
    }

    /**
     * @return int|null byte offset immediately after the opening tag's closing `]`
     */
    protected function findEndOfOpeningShortcodeTag(string $value, int $openBracket): ?int
    {
        $len = strlen($value);
        $i = $openBracket + 1;
        if ($i >= $len) {
            return null;
        }

        if ($value[$i] === '[') {
            return null;
        }

        $quote = null;
        for (; $i < $len; $i++) {
            $c = $value[$i];
            if ($quote !== null) {
                if ($c === '\\') {
                    $i++;

                    continue;
                }
                if ($c === $quote) {
                    $quote = null;
                }

                continue;
            }
            if ($c === '"' || $c === "'") {
                $quote = $c;

                continue;
            }
            if ($c === ']') {
                return $i + 1;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null  preg-style match array plus `_end` (exclusive end offset in $value)
     */
    protected function composeShortcodeMatch(string $value, int $openBracket, int $openEnd, string $name): ?array
    {
        $openTag = substr($value, $openBracket, $openEnd - $openBracket);
        $isSelfClosing = (bool) preg_match('#/\s*\]\s*$#', $openTag);

        $nameStart = $openBracket + 1;
        if ($nameStart + strlen($name) > $openEnd) {
            return null;
        }

        if (strcasecmp(substr($value, $nameStart, strlen($name)), $name) !== 0) {
            return null;
        }

        $attrsStart = $nameStart + strlen($name);
        $attrsRaw = substr($value, $attrsStart, $openEnd - 1 - $attrsStart);
        if ($isSelfClosing) {
            $attrsRaw = rtrim($attrsRaw);
            $attrsRaw = preg_replace('#/\s*$#', '', $attrsRaw) ?? '';
        }

        if ($isSelfClosing) {
            $fullEnd = $openEnd;
            $content = '';

            return [
                0 => substr($value, $openBracket, $fullEnd - $openBracket),
                1 => '',
                2 => $name,
                3 => $attrsRaw,
                4 => '/',
                5 => $content,
                6 => '',
                '_end' => $fullEnd,
            ];
        }

        $balanced = $this->findBalancedClosingTag($value, $openEnd, $name);
        if ($balanced === null) {
            // Void shortcode: [tag attrs] with no matching [/tag] (GitHub #51).
            $fullEnd = $openEnd;

            return [
                0 => substr($value, $openBracket, $fullEnd - $openBracket),
                1 => '',
                2 => $name,
                3 => $attrsRaw,
                4 => '/',
                5 => '',
                6 => '',
                '_end' => $fullEnd,
            ];
        }

        [$closeStart, $closeEnd] = $balanced;
        $content = substr($value, $openEnd, $closeStart - $openEnd);
        $fullEnd = $closeEnd;

        return [
            0 => substr($value, $openBracket, $fullEnd - $openBracket),
            1 => '',
            2 => $name,
            3 => $attrsRaw,
            4 => '',
            5 => $content,
            6 => '',
            '_end' => $fullEnd,
        ];
    }

    /**
     * @return array{0: int, 1: int}|null  [closeBracketStart, exclusiveEndAfterCloseTag]
     */
    protected function findBalancedClosingTag(string $value, int $contentStart, string $name): ?array
    {
        $len = strlen($value);
        $depth = 1;
        $pos = $contentStart;
        $openRe = '/\[(?!\/)' . preg_quote($name, '/') . '(?![\w-])/i';
        $closeRe = '/\[\/' . preg_quote($name, '/') . '\]/i';

        while ($pos < $len && $depth > 0) {
            $hasOpen = preg_match($openRe, $value, $mo, PREG_OFFSET_CAPTURE, $pos);
            $hasClose = preg_match($closeRe, $value, $mc, PREG_OFFSET_CAPTURE, $pos);

            if (!$hasClose) {
                return null;
            }

            $openPos = $hasOpen ? (int) $mo[0][1] : PHP_INT_MAX;
            $closePos = (int) $mc[0][1];

            if ($hasOpen && $openPos < $closePos) {
                $depth++;
                $nextOpenEnd = $this->findEndOfOpeningShortcodeTag($value, $openPos);
                if ($nextOpenEnd === null) {
                    return null;
                }
                $pos = $nextOpenEnd;

                continue;
            }

            $depth--;
            $closeEnd = $closePos + strlen($mc[0][0]);
            if ($depth === 0) {
                return [$closePos, $closeEnd];
            }
            $pos = $closeEnd;
        }

        return null;
    }

    /**
     * Get registered shortcodes
     *
     * @return array shortcode tags.
     */
    public function getRegistered()
    {
        return $this->registered;
    }
}
