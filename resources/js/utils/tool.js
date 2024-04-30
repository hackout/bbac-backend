import dayjs from "dayjs";
import Cookies from "js-cookie";
import { ElMessage } from "element-plus";
import html2canvas from 'html2canvas';
import JsPDF from 'jspdf';
import * as XLSX from "xlsx";
import { saveAs } from 'file-saver'

let tool = {}

/* Cookies */
tool.cookies = {
    get(name, value = false) {
        return Cookies.get(name) ?? value
    },
    set(name, value, options = { expires: 7 }) {
        return Cookies.set(name, value, options)
    },
    remove(name) {
        return Cookies.remove(name)
    }
}

/* localStorage */
tool.data = {
    set(key, data, dateTime = 0) {
        let cacheValue = {
            content: data,
            dateTime: parseInt(dateTime) === 0 ? 0 : new Date().getTime() + parseInt(dateTime) * 1000
        }
        return localStorage.setItem(key, JSON.stringify(cacheValue))
    },
    get(key) {
        try {
            const value = JSON.parse(localStorage.getItem(key))
            if (value) {
                let nowTime = new Date().getTime()
                if (nowTime > value.dateTime && value.dateTime != 0) {
                    localStorage.removeItem(key)
                    return null;
                }
                return value.content
            }
            return null
        } catch (err) {
            return null
        }
    },
    remove(key) {
        return localStorage.removeItem(key)
    },
    clear() {
        return localStorage.clear()
    }
}

/*sessionStorage*/
tool.session = {
    set(table, settings) {
        var _set = JSON.stringify(settings)
        return sessionStorage.setItem(table, _set);
    },
    get(table) {
        var data = sessionStorage.getItem(table);
        try {
            data = JSON.parse(data)
        } catch (err) {
            return null
        }
        return data;
    },
    remove(table) {
        return sessionStorage.removeItem(table);
    },
    clear() {
        return sessionStorage.clear();
    }
}
/* Fullscreen */
tool.screen = function (element) {
    var isFull = !!(document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || document.fullscreenElement);
    if (isFull) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    } else {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        }
    }
}

/* 复制对象 */
tool.objCopy = function (obj) {
    return JSON.parse(JSON.stringify(obj));
}

/* 日期格式化 */
tool.dateFormat = function (date, fmt = 'YYYY-MM-DD HH:mm:ss') {
    if (!date || (typeof date != 'string' && typeof date != 'number')) return '-'
    return dayjs(date).format(fmt)
}

/* 带时间差异格式化 */
tool.dateFormatPlus = function (date, fmt = 'YYYY-MM-DD HH:mm:ss') {
    if (!date || (typeof date != 'string' && typeof date != 'number')) return '-'
    let now = dayjs()
    let current = dayjs(date)
    let diffHours = now.diff(current, 'hour')
    let diffMinutes = now.diff(current, 'minute')
    if (diffMinutes < 1) return '刚刚'
    if (diffMinutes < 10) return diffMinutes + '分钟前'
    if (diffHours < 5) return diffHours + '小时前'
    return current.format(fmt)
}

/* 万元缩进 */
tool.divMyriad = function (num) {
    return parseFloat((parseFloat(num) / 10000).toFixed(2))
}

/* 千分符 */
tool.groupSeparator = function (num) {
    num = num + '';
    if (!num.includes('.')) {
        num += '.'
    }
    return num.replace(/(\d)(?=(\d{3})+\.)/g, function ($0, $1) {
        return $1 + ',';
    }).replace(/\.$/, '');
}

tool.fileToBase64 = (file, callback) => {
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        const response = {
            status: true,
            data: reader.result
        }
        callback(response);
    };
    reader.onerror = function () {
        const response = {
            status: false,
            data: reader.error
        }
        callback(response);
    };
}
//秒转时长
tool.splitTime = (time) => {
    if (!time) {
        return 0
    }
    const hour = 3600
    const min = 60
    let _h = parseInt(time / hour);
    let _m = parseInt(time % hour / min);
    let _s = parseInt(time % hour % min);
    const arr = [_h < 10 ? '0' + _h : _h, _m < 10 ? '0' + _m : _m, _s < 10 ? '0' + _s : _s];
    return arr.join(':');
}
//splitTime逆向
tool.stringTime = (time) => {
    if (!time) {
        return 0
    }
    const times = time.split(':');
    let result = 0
    if (times.length == 3) {
        result = parseInt(times[2])
        result += parseInt(times[1]) * 60;
        result += parseInt(times[0]) * 60 * 60;
    } else if (times.length == 2) {
        result = parseInt(times[1]);
        result += parseInt(times[0]) * 60;
    } else {
        result = parseInt(times[0]);
    }
    return result
}

tool.byteString = (bytes) => {
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return '0 Bytes';
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}

tool.success = (page) => {
    var name = Object.keys(page)[0]
    ElMessage.success(typeof page[name] == 'object' ? page[name][0] : page[name])
}
tool.error = (page) => {
    var name = Object.keys(page)[0]
    ElMessage.error(typeof page[name] == 'object' ? page[name][0] : page[name])
}

/**
 * 获取设备DPI
 * @returns 
 */
tool.initDpi = () => {
    const div = document.createElement('div')
    div.style.cssText = 'height: 1in; left: -100%; position: absolute; top: -100%; width: 1in;'
    document.body.appendChild(div)
    const devicePixelRatio = window.devicePixelRatio || 1,
        dpi = div.offsetWidth * devicePixelRatio;
    var style = document.createElement('style');
    style.innerHTML = `:root{
        --web-pixel-ratio: ${dpi};
    }`;
    document.head.appendChild(style);
}

/**
 * 保存元素到PDF
 * @param {documentElement} ele 
 * @param {string} pdfName 
 */
tool.makePDF = (ele, pdfName) => {
    let eleW = ele.offsetWidth;
    let eleH = ele.offsetHeight;
    let eleOffsetTop = ele.offsetTop;
    let eleOffsetLeft = ele.offsetLeft;
    var canvas = document.createElement("canvas");
    var abs = 0;
    let win_in = document.documentElement.clientWidth || document.body.clientWidth;
    let win_out = window.innerWidth;
    if (win_out > win_in) {
        abs = (win_out - win_in) / 2;
    }
    canvas.width = eleW * 2;
    canvas.height = eleH * 2;
    var context = canvas.getContext("2d");
    context.scale(2, 2);
    context.translate(-eleOffsetLeft - abs, -eleOffsetTop);
    html2canvas(ele, {
        dpi: 300,
        scale: 3,
        useCORS: true
    }).then((canvas) => {

        var contentWidth = canvas.width;
        var contentHeight = canvas.height;
        var pageHeight = contentWidth / 592.28 * 841.89;
        var leftHeight = contentHeight;
        var position = 0;
        var imgWidth = 595.28;
        var imgHeight = 595.28 / contentWidth * contentHeight;
        var pageData = canvas.toDataURL('image/jpeg', 1.0);
        var pdf = new JsPDF('', 'pt', 'a4');
        if (leftHeight < pageHeight) {
            pdf.addImage(pageData, 'JPEG', 0, 40, imgWidth, imgHeight);
        } else {    // 分页
            while (leftHeight > 0) {
                pdf.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight);
                leftHeight -= pageHeight;
                position -= 841.89;
                if (leftHeight > 0) {
                    pdf.addPage();
                }
            }
        }
        pdf.save(pdfName);
    })

}

/**
 * 导出Excel
 * @param {string} tableId 
 * @param {string} fileName 
 */
tool.makeExcel = (tableId, fileName) => {
    var wb = XLSX.utils.table_to_book(document.querySelector(tableId),{raw:true});
    var wbOut = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) {
            view[i] = s.charCodeAt(i) & 0xff;
        }
        return buf;
    }

    saveAs(new Blob([s2ab(wbOut)], { type: 'application/octet-stream' }), fileName);
}



export default tool
