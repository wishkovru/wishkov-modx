<?php
/**
 * Wishkov button: PHP port of the reference button.html (copy, do not edit; source: integrations/button/php/WishkovButton.php).
 *
 * @copyright  Wishkov, https://wishkov.ru
 * @license    MIT (button class); GNU General Public License version 2 or later (module)
 */

namespace Wishkov;

final class WishkovButton
{
    public const SITE = 'https://wishkov.ru';
    public const PLACEHOLDER_URL = '{{URL}}';
    public const DEFAULT_LABEL = 'В вишлист';
    public const DEFAULT_REF = 'button';

    // Цвета — токены static/css/wishkov.css сайта: --violet, --violet-deep, --violet-50, --violet-100, --white.
    private const VIOLET = '#6E56E8';
    private const VIOLET_DEEP = '#3E2E96';
    private const PAPER = '#F7F5FF';
    private const SIZES = [
        's' => ['height' => 32, 'pad' => 12, 'font' => 13, 'icon' => 18],
        'm' => ['height' => 40, 'pad' => 16, 'font' => 15, 'icon' => 22],
    ];
    private const THEMES = [
        'fill' => ['bg' => '#6E56E8', 'fg' => '#ffffff', 'border' => '#6E56E8'],
        'outline' => ['bg' => '#ffffff', 'fg' => '#3E2E96', 'border' => '#6E56E8'],
        'soft' => ['bg' => '#EFEBFF', 'fg' => '#3E2E96', 'border' => '#EFEBFF'],
    ];
    private const THEME_ALIASES = ['light' => 'fill', 'dark' => 'soft'];

    /** Размеры кнопки: высота, отступ, кегль, сторона знака (px). */
    public static function sizes(): array
    {
        return self::SIZES;
    }

    /** Три дизайна (fill, outline, soft) с цветами bg, fg, border. */
    public static function themes(): array
    {
        return self::THEMES;
    }

    /** Синонимы light/dark -> fill/soft; неизвестное имя -> fill. */
    public static function normalizeTheme(string $theme): string
    {
        if (isset(self::THEME_ALIASES[$theme])) {
            $theme = self::THEME_ALIASES[$theme];
        }
        return isset(self::THEMES[$theme]) ? $theme : 'fill';
    }

    /** Как urllib.parse.quote(safe=''). */
    public static function encode(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * @param string $url     адрес товара; пустая строка даёт заполнитель {{URL}}
     * @param array  $options label, size (s|m), theme (fill|outline|soft; light и dark — синонимы), icon_only (bool), ref
     */
    public static function render(string $url = self::PLACEHOLDER_URL, array $options = []): string
    {
        $url = $url === '' ? self::PLACEHOLDER_URL : $url;
        $dims = self::SIZES[$options['size'] ?? 'm'] ?? self::SIZES['m'];
        $theme = self::normalizeTheme((string) ($options['theme'] ?? 'fill'));
        $colors = self::THEMES[$theme];
        $iconOnly = !empty($options['icon_only']);
        $label = trim((string) ($options['label'] ?? self::DEFAULT_LABEL));
        $label = self::cut($label, 60);
        if ($label === '') {
            $label = self::DEFAULT_LABEL;
        }
        $pad = $iconOnly ? '0' : '0 ' . $dims['pad'] . 'px';
        $width = $iconOnly ? 'width:' . $dims['height'] . 'px;' : '';
        $style = 'display:inline-flex;align-items:center;justify-content:center;gap:8px;box-sizing:border-box;'
            . 'height:' . $dims['height'] . 'px;' . $width . 'padding:' . $pad . ';border-radius:999px;'
            . 'background:' . $colors['bg'] . ';color:' . $colors['fg'] . ';border:1.5px solid ' . $colors['border'] . ';'
            . 'font:700 ' . $dims['font'] . "px/1 Mulish,system-ui,-apple-system,'Segoe UI',sans-serif;"
            . 'text-decoration:none;white-space:nowrap;vertical-align:middle';
        $safeLabel = self::escape($label);
        $text = $iconOnly ? '' : '<span>' . $safeLabel . '</span>';
        $aria = $iconOnly ? ' aria-label="' . $safeLabel . '"' : '';

        return '<a href="' . self::escape(self::href($url, (string) ($options['ref'] ?? self::DEFAULT_REF)))
            . '" rel="nofollow" title="' . $safeLabel . '"' . $aria . ' style="' . $style . '">'
            . self::markSvg($dims['icon'], $theme) . $text . '</a>';
    }

    /** Неэкранированный адрес кнопки: заполнитель как есть, настоящий адрес через rawurlencode. */
    public static function href(string $url = self::PLACEHOLDER_URL, string $ref = self::DEFAULT_REF): string
    {
        $urlPart = $url === self::PLACEHOLDER_URL ? $url : self::encode($url);
        $ref = trim($ref);
        if (!preg_match('/^[a-z0-9_-]{1,20}$/', $ref)) {
            $ref = self::DEFAULT_REF;
        }
        return self::SITE . '/add/?url=' . $urlPart . '&ref=' . $ref;
    }

    /** Как django.utils.html.escape: & < > " ' . */
    private static function escape(string $text): string
    {
        return str_replace(
            ['&', '<', '>', '"', "'"],
            ['&amp;', '&lt;', '&gt;', '&quot;', '&#x27;'],
            $text
        );
    }

    /** Цветной знак; на заливке корпус светлый, окошки фиолетовые, на остальных наоборот. */
    public static function markSvg(int $size, string $theme): string
    {
        $isFill = self::normalizeTheme($theme) === 'fill';
        $body = $isFill ? self::PAPER : self::VIOLET;
        $window = $isFill ? self::VIOLET : self::PAPER;
        $win = function ($x, $y) use ($window) {
            return '<rect x="' . $x . '" y="' . $y . '" width="30" height="30" rx="8" fill="' . $window . '"/>';
        };
        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 200 200" fill="none" aria-hidden="true" focusable="false">'
            . '<rect x="44" y="30" width="112" height="140" rx="18" fill="' . $body . '"/>'
            . '<rect x="72" y="18" width="56" height="24" rx="8" fill="' . self::VIOLET_DEEP . '"/>'
            . $win(66, 72) . $win(104, 72) . $win(66, 112) . $win(104, 112)
            . '</svg>';
    }

    private static function cut(string $text, int $limit): string
    {
        return function_exists('mb_substr') ? mb_substr($text, 0, $limit) : substr($text, 0, $limit);
    }
}
