<?php
namespace App\Packages\ImagePlus;

use App\Models\User;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ImagePlus
{
    private ImageManager $manager;

    public function __construct(
        private string $templatePath
    ) {
        $driver = \extension_loaded('imagick') ? 'imagick' : 'gd';
        $this->manager = new ImageManager(['driver' => $driver]);
    }

    public function makeBirthdayCard(User $user, array $data)
    {
        $image = $this->manager->make($this->templatePath);
        $image->resize(500, 400);
        $fileName = $user->number . '-' . Str::uuid() . '.jpg';
        $filePath = storage_path('app/public/cards');
        if (!is_dir($filePath)) {
            @mkdir($filePath);
        }
        $newFilePath = $filePath . '/' . $fileName;
        $texts = [];
        foreach ($data as $rs) {
            $text = $this->getValue($rs, $user);
            if ($text) {
                $texts[] = [
                    'text' => is_array($text) ? implode(PHP_EOL, $text) : $text,
                    'color' => $rs['color'],
                    'fontSize' => $rs['fontSize'],
                    'width' => $rs['width'],
                    'height' => $rs['height'],
                    'left' => $rs['left'],
                    'top' => $rs['top'],
                ];
            }
        }
        if ($texts) {
            foreach ($texts as $text) {
                $x = (float) $text['left'];
                $y = (float) $text['top'] + (float) (bcmul($text['height'], 0.75, 2));
                $image->text($text['text'], $x, $y, function ($font) use ($text) {
                    $font->file(public_path('assets/font/HarmonyOS_Sans_SC_Regular.ttf'));
                    $font->size((float) $text['fontSize']);
                    $font->color($text['color']);
                    $font->align('left');
                    $font->valign('bottom');
                });
            }
        }
        $image->save($newFilePath);
        return $newFilePath;
    }

    private function getValue(array $rs, User $user): string|array|null
    {
        $value = null;
        switch ($rs['code']) {
            case 'name':
                $value = optional($user->profile)->name;
                break;
            case 'number':
                $value = $user->number;
                break;
            case 'input':
                $value = $rs['name'];
                break;
        }
        return $value;
    }
}