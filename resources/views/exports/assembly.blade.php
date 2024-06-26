<table border="1">
    <thead>
        <tr>
            <th bgcolor="#ffff00" height="40px" align="center" valign="center" colspan="9">总成信息记录</th>
        </tr>
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">工厂</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">产线</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">机型</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">状态</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="200px">总成号</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="200px">备注信息</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">发动机数量</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">录入时间</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">更新时间</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $index => $item)
            <tr>
                <td align="center" valign="center" height="30px" width="100px">{{ $item->plant }}
                </td>
                <td align="center" valign="center" height="30px" width="100px">{{ $item->line }}
                </td>
                <td align="center" valign="center" height="30px" width="100px">{{ $item->type }}
                </td>
                <td align="center" valign="center" height="30px" width="100px">{{ $item->status }}
                </td>
                <td align="center" valign="center" height="30px" width="200px">{{ $item->number }}
                </td>
                <td align="center" valign="center" height="30px" width="200px">{{ $item->remark }}
                </td>
                <td align="center" valign="center" height="30px" width="100px"> {{ $item->product_count }}</td>
                <td align="center" valign="center" height="30px" width="150px"> {{ $item->created_at }}</td>
                <td align="center" valign="center" height="30px" width="150px"> {{ $item->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
