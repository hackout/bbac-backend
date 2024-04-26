<table border="1">
    <thead>
        <tr>
            <th bgcolor="#ffff00" height="40px" align="center" valign="center" colspan="3">语言包</th>
        </tr>
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="200px">标识</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="300px">中文</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="300px">英文</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td align="center" valign="center" height="30px" width="200px">
                    {{ $item->code }}
                </td>
                <td align="center" valign="center" height="30px" width="300px">
                    {{ $item->content_zh }}
                </td>
                <td align="center" valign="center" height="30px" width="300px">
                    {{ $item->content_en }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
