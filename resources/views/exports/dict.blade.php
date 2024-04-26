<table border="1">
    <thead>
        <tr>
            <th bgcolor="#ffff00" height="40px" align="center" valign="center" colspan="4">字典信息</th>
        </tr>
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" rowspan="2" width="100px">字典名称</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" rowspan="2" width="100px">字典标识</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" colspan="2" width="400px">字典项</th>
        </tr>
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="300px">键名</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">键值</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td align="center" valign="center" rowspan="{{ $item->items->count() > 1 ? $item->items->count() + 1 : 1 }}" height="30px" width="100px">
                    {{ $item->name }}
                </td>
                <td align="center" valign="center" rowspan="{{ $item->items->count() > 1 ? $item->items->count() + 1 : 1 }}" height="30px" width="100px">
                    {{ $item->code }}
                </td>
                <td align="center" valign="center" height="30px" width="300px">
                    {{ optional($item->items->first())->name }}
                </td>
                <td align="center" valign="center" height="30px" width="100px">
                    {{ optional($item->items->first())->content }}
                </td>
            </tr>
            @if ($item->items->count() > 1)
                @foreach ($item->items as $_item)
                    <tr>
                        <td align="center" valign="center" height="30px" width="300px">
                            {{ $_item->name }}
                        </td>
                        <td align="center" valign="center" height="30px" width="100px">
                            {{ $_item->content }}
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
