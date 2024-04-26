<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Account;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\OnEachRow;

class UserImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        if ($row->getIndex() > 2) {
            list($number, $username, $email, $mobile) = array_pad($row->toArray(), 4, null);

            $accounts = [];
            if ($number) {
                $accountSql = [
                    'type' => Account::NUMBER,
                    'account' => $number
                ];
                if (Account::where($accountSql)->first()) {
                    return;
                }
                $accounts[] = $accountSql;
            }
            if ($username) {
                $accountSql = [
                    'type' => Account::ACCOUNT,
                    'account' => $username
                ];
                if (Account::where($accountSql)->first()) {
                    return;
                }
                $accounts[] = $accountSql;
            }
            if ($email) {
                $accountSql = [
                    'type' => Account::EMAIL,
                    'account' => $email
                ];
                if (Account::where($accountSql)->first()) {
                    return;
                }
                $accounts[] = $accountSql;
            }
            if ($mobile) {
                $accountSql = [
                    'type' => Account::MOBILE,
                    'account' => $mobile
                ];
                if (Account::where($accountSql)->first()) {
                    return;
                }
                $accounts[] = $accountSql;
            }
            if (!$accounts) {
                return;
            }
            $sql = [
                'password' => Hash::make(env('DEFAULT_PASSWORD'))
            ];
            if ($user = User::create($sql)) {
                $user->accounts()->createMany($accounts);
            }
            $cacheName = (new User)->getTable();
            $cache = Cache::get($cacheName, []);
            foreach ($cache as $key) {
                Cache::forget($key);
            }
            Cache::forget($cacheName);
        }
    }
}
