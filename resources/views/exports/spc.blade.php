<table border="1">
    <thead>
        <tr>
            <th bgcolor="#ffff00" height="40px" align="center" valign="center" colspan="43">排产计划记录</th>
        </tr>
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="100px">工厂</th>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="100px">产线</th>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="100px">机型</th>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="200px">总成号</th>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="200px">螺栓编号</th>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="150px">中文描述</th>
            <th align="center" bgcolor="#ff8000" valign="center" rowspan="2" height="30px" width="150px">英文描述</th>
            @foreach ($months as $month)
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" colspan="3" width="450px">{{ $month['name'] }}月份</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($months as $month)
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">备注</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">CP</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">CPK</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $index => $item)
            <tr>
                <td align="center" valign="center" height="30px" width="100px">{{ $item['plant'] }}
                </td>
                <td align="center" valign="center" height="30px" width="100px">{{ $item['line'] }}
                </td>
                <td align="center" valign="center" height="30px" width="100px">{{ $item['engine'] }}
                </td>
                <td align="center" valign="center" height="30px" width="200px">{{ $item['assembly_id'] }}
                </td>
                <td align="center" valign="center" height="30px" width="200px">{{ $item['number'] }}
                </td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['content_zh'] }}
                </td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['content_en'] }}
                </td>
                @foreach ($months as $month)
                <td align="center" valign="center" height="30px" width="150px"> {{ $item['months'][$month['name']]['remark'] }}</td>
                <td align="center" valign="center" height="30px" width="150px"> {{ $item['months'][$month['name']]['cp'] }}</td>
                <td align="center" valign="center" height="30px" width="150px"> {{ $item['months'][$month['name']]['cpk'] }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
