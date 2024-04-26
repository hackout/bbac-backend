import dayjs from "dayjs";
import Cookies from "js-cookie";
import { ElMessage } from "element-plus";

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
export default tool
