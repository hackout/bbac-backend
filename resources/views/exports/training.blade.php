<table border="1">
    <thead>
        @if ($type == 3)
            <tr>
                <th bgcolor="#ffff00" height="60px" align="center" valign="center"
                    colspan="{{ 6 + (count($users) ? count($users) + 1 : 0) }}">综合培训记录表</th>
            </tr>
        @endif
        @if ($type == 2)
            <tr>
                <th bgcolor="#ffff00" height="60px" align="center" valign="center"
                    colspan="{{ 6 + (count($users) ? count($users) + 1 : 0) }}">技能培训记录表</th>
            </tr>
        @endif
        @if ($type == 1)
            <tr>
                <th bgcolor="#ffff00" height="60px" align="center" valign="center"
                    colspan="{{ 6 + (count($users) ? count($users) + 1 : 0) }}">安全培训记录表</th>
            </tr>
        @endif
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">序号</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="200px">培训内容</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="200px">培训时间</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">培训周期(天)
            </th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">状态</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="200px">完成时间</th>
            @if (count($users))
                @foreach ($users as $user)
                    <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">
                        {{ $user['name'] }}</th>
                @endforeach
                <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">班组完成率
                </th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $index => $item)
            <tr>
                <td align="center" valign="center" height="30px" width="100px">
                    {{ $index + 1 }}</td>
                <td align="center" valign="center" height="30px" width="200px">
                    {{ $item['name'] }}</td>
                <td align="center" valign="center" height="30px" width="200px">
                    {{ $item['started_at'] }}</td>
                <td align="center" valign="center" height="30px" width="100px">
                    {{ $item['period'] }}</td>
                <td align="center" valign="center" height="30px" width="100px">
                    {{ $status->where('value', $item['status'])->value('name') ?? $item['status'] }}</td>
                <td align="center" valign="center" height="30px" width="200px">
                    {{ $item['ended_at'] }}</td>
                @if (count($users))
                    @foreach ($users as $user)
                        <td align="center" valign="center" height="30px" width="150px">
                            {{ $item_status->where('value', $item['users'][$user['value']])->value('name') ?? $item['users'][$user['value']] }}
                        </td>
                    @endforeach
                    <td align="center" valign="center" height="30px" width="100px">
                        {{ $item['rate'] }}%</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
