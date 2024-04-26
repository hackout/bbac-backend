<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"> 
<title>M254产品审核记录表单</title> 

<style>
    :root {
        --base-point: 45px;
    }

    body {
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        overflow-y: auto;
        padding: 20px 0;
        background: #c0c0c0;
    }

    .table-box {
        background: #FFF;
        position: relative;
    }

    .table-box::before {
        content: '第1页';
        font-size: calc(var(--base-point) * 0.51 * 4);
        color: #c0c0c0;
        font-family: '宋体';
        width: 400px;
        height: 400px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -200px;
        margin-top: -200px;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: calc(var(--base-point) * 0.51);
    }


    table {
        table-layout:fixed;text-overflow:clip;
        border: #000 2px solid;
        width: calc(var(--base-point) * 0.51 * 36);
        border-collapse: collapse;
        position: relative;
        z-index: 2;
    }
    th,
    td {
        width: calc(var(--base-point) * 0.51 * attr('colspan'));
        border-collapse: collapse;
        padding: 0 2px;
        box-sizing: border-box;
        line-height: 1.1;
        position: relative;
        overflow: hidden;
    }

    .title {
        font-size: 21px;
    }

    .middle {
        font-size: 14px;
    }

    .normal {
        font-size: 10px;
        font-family: Arial, "宋体", Helvetica, sans-serif,
    }

    .bold {
        font-weight: bold
    }

    .empty {
        border-color: #cccccc;
    }

    .empty-last {
        border-bottom: #000000 1px solid;
    }

    .height {
        height: calc(var(--base-point) * 0.88);
    }

    .height161 {
        height: calc(var(--base-point) * 1.61);
    }

    .height243 {
        height: calc(var(--base-point) * 2.43);
    }

    .height53 {
        height: calc(var(--base-point) * 0.53);
    }

    /*
1厘米 = 42
*/
</style>

</head>
<body>
    <div class="table-box">

        <table border="1" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th height="75px" class="height" colspan="9" align="center" valign="middle">
                        <img src="{{ url('/assets/imgs/benz.png') }}" height="75px" alt="">
                    </th>
                    <th colspan="27" class="title" align="center" valign="middle">
                        M254产品审核记录表<br />M254 Product Audit Recorded
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="height">
                    <td colspan="36" class="middle bold" align="center" valign="middle">
                        发动机信息/Engine Data
                    </td>
                </tr>
                <tr class="height">
                    <td colspan="9" class="middle" valign="middle">型号/Model</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                    <td colspan="9" class="middle" valign="middle">发动机号/Engine No.</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                </tr>
                <tr class="height">
                    <td colspan="9" class="middle" valign="middle">生产线/Assembly line</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                    <td colspan="9" class="middle" valign="middle">生产阶段/Phase</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                </tr>
                <tr class="height">
                    <td colspan="9" class="middle" valign="middle">装配日期/Assembly Date</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                    <td colspan="9" class="middle" valign="middle">热试日期/QC Date</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                </tr>
                <tr class="height">
                    <td colspan="9" class="middle" valign="middle">接机日期/Audit Beginning</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                    <td colspan="9" class="middle" valign="middle">考核员/Auditor</td>
                    <td colspan="9" class="middle" valign="middle"></td>
                </tr>
                <tr class="height">
                    <td colspan="36" class="middle bold" align="center" valign="middle">缺陷判定/Finding</td>
                </tr>
                <tr class="height">
                    <td colspan="7" class="middle" align="center" valign="middle">位置/Location</td>
                    <td colspan="22" class="middle" align="center" valign="middle">描述/Description</td>
                    <td colspan="7" class="middle" align="center" valign="middle">缺陷等级/Defect Class</td>
                </tr>
                <tr class="height">
                    <td colspan="7" class="middle" align="center" valign="middle"></td>
                    <td colspan="22" class="middle" align="center" valign="middle"></td>
                    <td colspan="7" class="middle" align="center" valign="middle"></td>
                </tr>
                <tr class="height161">
                    <td colspan="7" class="normal" align="center" valign="middle">缸压泄露率/Compression Loss
                        rate<br />参考值/Ref. Value：0-10%</td>
                    <td colspan="7" class="normal" align="center" valign="middle">火花塞间隙/Spark Plug Gap
                        (mm)<br />参考值/Ref. Value：0.65-0.75</td>
                    <td colspan="7" class="normal" align="center" valign="middle">连杆轴向间隙/Con-rod Axial Clearance
                        (mm)<br />参考值/Ref. Value ：0.05-0.31</td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr class="height">
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 1</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 1</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 1</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr class="height">
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 2</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 2</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 2</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                    <td class="empty"></td>
                </tr>
                <tr class="height">
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 3</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 3</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td colspan="2" class="normal" align="center" valign="middle">Cyl. 3</td>
                    <td colspan="5" class="normal" align="center" valign="middle"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                </tr>
                <tr class="height">
                    <td colspan="36" class="normal" align="center" valign="middle">间隙测量/Axial Clearance (mm)</td>
                </tr>
                <tr class="height243">
                    <td colspan="6" class="normal" align="center" valign="middle">参考值/Ref. Value</td>
                    <td colspan="8" class="normal" align="center" valign="middle">进气凸轮轴轴向间隙/Intake
                        Camshaft<br />0.05-0.20</td>
                    <td colspan="8" class="normal" align="center" valign="middle">排气凸轮轴轴向间隙/Exhaust
                        Camshaft<br />0.05-0.20</td>
                    <td colspan="8" class="normal" align="center" valign="middle">
                        曲轴轴轴向间隙/Crankshaft<br />0.1mm-0.273mm</td>
                    <td colspan="6" class="normal" align="center" valign="middle">检查OT后94°高压油泵柱塞平面高度<br />Check
                        roller tappet hight<br />参考值/Ref. Value 32.2-32.8mm</td>
                </tr>
                <tr class="height">
                    <td colspan="6" class="normal" align="center" valign="middle">实测值/Actual Value</td>
                    <td colspan="8" class="normal" align="center" valign="middle"></td>
                    <td colspan="8" class="normal" align="center" valign="middle"></td>
                    <td colspan="8" class="normal" align="center" valign="middle"></td>
                    <td colspan="6" class="normal" align="center" valign="middle"></td>
                </tr>
                <tr class="height">
                    <td colspan="21" class="middle bold empty empty-last" align="left" valign="middle">
                        目视检查/visual check</td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                    <td class="empty empty-last"></td>
                </tr>
                <tr class="height">
                    <td colspan="15" class="middle" align="center" valign="middle">曲轴轴瓦配瓦是否正确<br />/CS shell mark
                        right or not</td>
                    <td colspan="12" class="middle" align="center" valign="middle">连杆配重是否一致<br />/Con-rod weight
                        level coincident</td>
                    <td colspan="9" class="middle" align="center" valign="middle">使用工装检查平衡轴位置是否正确（仅E20)<br />Use
                        tooling check Lanchester position(Only for E20)</td>
                </tr>
                <tr class="height">
                    <td colspan="15" class="middle" align="center" valign="middle"></td>
                    <td colspan="12" class="middle" align="center" valign="middle"></td>
                    <td colspan="9" class="middle" align="center" valign="middle"></td>
                </tr>
                <tr class="height">
                    <td colspan="36" class="middle bold" align="center" valign="middle">备注/Remark</td>
                </tr>
                <tr class="height">
                    <td colspan="36" class="middle bold" align="center" valign="middle"></td>
                </tr>
                <tr class="height53">
                    <td colspan="5" class="normal" valign="middle">编制/Edit</td>
                    <td colspan="7" class="normal" valign="middle"></td>
                    <td colspan="5" class="normal" valign="middle">批准/Approval</td>
                    <td colspan="7" class="normal" valign="middle"></td>
                    <td colspan="5" class="normal" valign="middle">文件号/Doc. No.</td>
                    <td colspan="7" class="normal" valign="middle">E1601-R1016</td>
                </tr>
                <tr class="height53">
                    <td colspan="5" class="normal" valign="middle">审核/Check</td>
                    <td colspan="7" class="normal" valign="middle"></td>
                    <td colspan="5" class="normal" valign="middle">日期/Date</td>
                    <td colspan="7" class="normal" valign="middle"></td>
                    <td colspan="5" class="normal" valign="middle">版本/Version</td>
                    <td colspan="7" class="normal" valign="middle"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>