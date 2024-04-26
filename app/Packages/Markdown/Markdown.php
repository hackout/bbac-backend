<?php
namespace App\Packages\Markdown;

use League\HTMLToMarkdown\HtmlConverter;
use Illuminate\Support\Str;

class Markdown
{
    /**
     * Html转Markdown
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string      $content
     * @return string|null
     */
    public static function toMarkdown(string $content):string|null
    {
        $converter = new HtmlConverter(array('strip_tags' => true));
        return $converter->convert($content);
    }

    /**
     * Markdown转Html
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string      $content
     * @return string|null
     */
    public static function toHtml(string $content):string|null
    {
        return Str::of($content)->markdown([
            'html_input' => 'strip',
        ]);
    }

    /**
     * Html转Array
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $content
     * @return array|null
     */
    public static function HtmlToArray(string $content):array|null
    {
        return self::toArray(self::toMarkdown($content));
    }

    /**
     * Markdown转Array
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $content
     * @return array|null
     */
    public static function toArray(string $content):array|null
    {
        return explode("\n",$content);
    }

}