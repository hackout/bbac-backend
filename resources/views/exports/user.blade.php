<table border="1">
    <thead>
        <tr>
            <th bgcolor="#ffff00" height="40px" align="center" valign="center" colspan="25">员工信息资料</th>
        </tr>
        <tr>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="100px">工号</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">登录账号</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">姓名</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">电话号码</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">电子邮箱</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">部门</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">角色</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">性别</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">生日</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">民族</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">籍贯</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">家庭地址</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">证件号码</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">学历</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">学位</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">紧急联系人</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">紧急联系电话</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">综合技能等级</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">职业等级</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">参加工作时间</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">入职时间</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">最后登录时间</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">最后登录IP</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">账号状态</th>
            <th align="center" bgcolor="#ff8000" valign="center" height="30px" width="150px">录入时间</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $index => $item)
            <tr>
                <td align="center" valign="center" height="30px" width="100px">{{ $item['number'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['username'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['name'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['mobile'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['email'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['department'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['roles'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['gender'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['birth'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['nation'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['birthplace'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['address'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['id_card'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['educational'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['science'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['emergency_contact'] }}
                </td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['emergency_telephone'] }}
                </td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['skill_level'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['career_level'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['attend_date'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['entry_date'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['lasted_login'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['lasted_ip_address'] }}
                </td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['is_valid'] }}</td>
                <td align="center" valign="center" height="30px" width="150px">{{ $item['created_at'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
