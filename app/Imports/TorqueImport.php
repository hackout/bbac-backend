<?php

namespace App\Imports;

use File;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Torque;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\Private\DictService;
use Illuminate\Support\Facades\Storage;
use App\Services\Private\AssemblyService;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Services\Private\TorqueChangeRecordService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class TorqueImport implements ToCollection
{
    public function __construct(private User $user,private UploadedFile $uploadedFile){}

    private function getDrawings()
    {
        $objRead = IOFactory::createReader('Xlsx');
        $obj = $objRead->load($this->uploadedFile);
        $currSheet = $obj->getActiveSheet();
        $array = [];
        $i = 0;
        foreach ($currSheet->getDrawingCollection() as $drawing) {
            if ($drawing instanceof MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();
                switch ($drawing->getMimeType()) {
                    case MemoryDrawing::MIMETYPE_PNG:
                        $extension = 'png';
                        break;
                    case MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif';
                        break;
                    case MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg';
                        break;
                }
            } else {
                if ($drawing->getPath()) {
                    if ($drawing->getIsURL()) {
                        $imageContents = file_get_contents($drawing->getPath());
                        $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                        file_put_contents($filePath , $imageContents);
                        $mimeType = mime_content_type($filePath);
                        $extension = File::mime2ext($mimeType);
                        unlink($filePath);            
                    }
                    else {
                        $zipReader = fopen($drawing->getPath(),'r');
                        $imageContents = '';
                        while (!feof($zipReader)) {
                            $imageContents .= fread($zipReader,1024);
                        }
                        fclose($zipReader);
                        $extension = $drawing->getExtension();            
                    }
                }
            }
            $path = Storage::path('public/imports/');
            if(!is_dir($path))
            {
                @mkdir($path);
            }
            $fileName = Storage::path('public/imports/'.Str::Uuid().'.'.$extension);
            file_put_contents($fileName,$imageContents);
            $key = str_replace(range('A','Z'),'', $drawing->getCoordinates());
            if(!isset($array[$key]))
            {
                $array[$key] = [$fileName];
            }else{
                $array[$key][] = $fileName;
            }
        }

        return $array;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $drawings = $this->getDrawings();
        $dictService = new DictService;
        $dicts = [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'model' => $dictService->getOptionByCode('bolt_model'),
            'type' => $dictService->getOptionByCode('bolt_type'),
            'status' => $dictService->getOptionByCode('bolt_status'),
            'stage' => $dictService->getOptionByCode('assembly_status'),
            'special' => $dictService->getOptionByCode('special'),
            'assemblies' => (new AssemblyService)->getOptions()
        ];
        $collection->each(function ($row, $index) use ($dicts,$drawings) {
            if ($index > 2) {
                list ($plant, $line, $engine, $assembly_id, $vehicle_type, $number, $content_zh, $content_en, $quantity, $model, $type, $status, $stage, $station, $sub_station, $special, $param, $torque_target, $torque_lower, $torque_upper, $angle_target, $angle_lower, $angle_upper, $lasted_at, $expected_at, $final_at,$pictures, $start_torque, $residual_torque, $pfu_test, $pfu_lower, $pfu_upper, $pfu_early_lower, $pfu_early_upper, $l_pfu_test, $l_pfu_lower, $l_pfu_upper, $l_pfu_early_lower, $l_pfu_early_upper) = array_pad($row->toArray(), 39, null);
                unset($pictures);
                $plant = $dicts['plant']->where('name', trim($plant))->value('value');
                $line = $dicts['line']->where('name', trim($line))->value('value');
                $engine = $dicts['engine_type']->where('name', trim($engine))->value('value');
                $vehicle_type = $dicts['motorcycle_type']->where('name', trim($vehicle_type))->value('value');
                $assembly_id = $dicts['assemblies']->where('name', trim($assembly_id))->value('value');
                $number = trim($number);
                $content_zh = trim($content_zh);
                $content_en = trim($content_en);
                $quantity = intval(trim($quantity));
                $model = $dicts['model']->where('name', trim($model))->value('value');
                $type = $dicts['type']->where('name', trim($type))->value('value');
                $status = $dicts['status']->where('name', trim($status))->value('value');
                $stage = $dicts['stage']->where('name', trim($stage))->value('value');
                $station = trim($station);
                $sub_station = trim($sub_station);
                $special = $dicts['special']->where('name', trim($special))->value('value');
                $param = trim($param);
                $torque_target = (float) trim($torque_target);
                $torque_lower = (float) trim($torque_lower);
                $torque_upper = (float) trim($torque_upper);
                $angle_target = (float) trim($angle_target);
                $angle_lower = (float) trim($angle_lower);
                $angle_upper = (float) trim($angle_upper);
                $lasted_at = trim($lasted_at) ? Carbon::parse("1900-01-01")->addDays(trim($lasted_at)) : null;
                $expected_at = trim($expected_at) ? Carbon::parse("1900-01-01")->addDays(trim($expected_at)) : null;
                $final_at = trim($final_at) ? Carbon::parse("1900-01-01")->addDays(trim($final_at)) : null;
                $start_torque = (float) trim($start_torque);
                $residual_torque = (float) trim($residual_torque);
                $pfu_test = (float) trim($pfu_test);
                $pfu_lower = (float) trim($pfu_lower);
                $pfu_upper = (float) trim($pfu_upper);
                $pfu_early_lower = (float) trim($pfu_early_lower);
                $pfu_early_upper = (float) trim($pfu_early_upper);
                $l_pfu_test = (float) trim($l_pfu_test);
                $l_pfu_lower = (float) trim($l_pfu_lower);
                $l_pfu_upper = (float) trim($l_pfu_upper);
                $l_pfu_early_lower = (float) trim($l_pfu_early_lower);
                $l_pfu_early_upper = (float) trim($l_pfu_early_upper);
                if ($plant !== null && $line !== null && $engine !== null && $content_zh !== null && $content_en != null && $assembly_id != null) {
                    $sql = [
                        'user_id' => $this->user->id,
                        'plant' => $plant,
                        'line' => $line,
                        'engine' => $engine,
                        'assembly_id' => $assembly_id,
                        'vehicle_type' => $vehicle_type ?? 0,
                        'number' => $number,
                        'content_zh' => $content_zh,
                        'content_en' => $content_en,
                        'quantity' => $quantity,
                        'model' => $model,
                        'type' => $type,
                        'status' => $status,
                        'stage' => $stage,
                        'station' => $station,
                        'sub_station' => $sub_station,
                        'special' => $special,
                        'param' => $param,
                        'torque_target' => $torque_target,
                        'torque_lower' => $torque_lower,
                        'torque_upper' => $torque_upper,
                        'angle_target' => $angle_target,
                        'angle_lower' => $angle_lower,
                        'angle_upper' => $angle_upper,
                        'lasted_at' => $lasted_at,
                        'expected_at' => $expected_at,
                        'final_at' => $final_at,
                        'start_torque' => $start_torque,
                        'residual_torque' => $residual_torque,
                        'pfu_test' => $pfu_test,
                        'pfu_lower' => $pfu_lower,
                        'pfu_upper' => $pfu_upper,
                        'pfu_early_lower' => $pfu_early_lower,
                        'pfu_early_upper' => $pfu_early_upper,
                        'l_pfu_test' => $l_pfu_test,
                        'l_pfu_lower' => $l_pfu_lower,
                        'l_pfu_upper' => $l_pfu_upper,
                        'l_pfu_early_lower' => $l_pfu_early_lower,
                        'l_pfu_early_upper' => $l_pfu_early_upper,
                    ];
                    if($torque = Torque::where('number',$number)->first())
                    {
                        (new TorqueChangeRecordService)->makeRecordByTorque($torque,$this->user,$sql);
                    }else{
                        $sql['author_id'] = $this->user->id;
                        $torque = Torque::create($sql);
                        if($torque && isset($drawings[$index + 1]))
                        {
                            foreach($drawings[$index + 1] as $drawing)
                            {
                                $torque->addMedia($drawing)->toMediaCollection(Torque::MEDIA_FILE);
                            }
                        }
                    }
                }
            }
        });
        $cacheName = (new Torque)->getTable();
        $cache = Cache::get($cacheName, []);
        foreach ($cache as $key) {
            Cache::forget($key);
        }
        Cache::forget($cacheName);
    }
}