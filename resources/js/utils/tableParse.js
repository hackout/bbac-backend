/**
 * HTML Table转Excel导入数据组
 */
let tableParse = {
    /**
     * 转换元素名称
     * 支持.className,#idName,tagName
     * @param {string} stringName 
     * @returns 
     */
    make: (stringName) => {
        let element = document.querySelector(stringName)
        if (!element) {
            throw Error("未查找到元素")
        }
        return tableParse.parse(element)
    },
    /**
     * 解析元素
     * @param {HTMLElement} element 
     * @returns 
     */
    parse: (element) => {
        if (element.localName != 'table') {
            throw Error("元素非Table标签")
        }
        let style = window.getComputedStyle(element);
        let result = {
            width: style.width,
            height: style.height,
            borderColor: style.borderColor,
            borderWidth: style.borderWidth,
            backgroundColor: style.backgroundColor
        }
        if (element.children.length > 0) {
            result['children'] = tableParse.childrenParse(element.children)
        }
        return result;
    },
    /**
     * 解析子级
     * @param {HTMLElement<>} array 
     * @returns
     */
    childrenParse: (array) => {
        let children = [];
        for (let i in array) {

            switch (array[i].localName) {
                case 'thead':
                case 'tbody':
                case 'tfoot':
                    children.push(tableParse.bodyParse(array[i]))
                    break;
                case 'tr':
                    children.push(tableParse.rowParse(array[i]))
                    break;
                case 'td':
                case 'th':
                    children.push(tableParse.colParse(array[i]))
                    break;
                case 'img':
                    children.push(tableParse.imgParse(array[i]))
                    break;
                case 'div':
                case 'span':
                    children.push(tableParse.textParse(array[i]))
                    break;
            }
        }
        return children
    },
    bodyParse: (element) => {
        let style = window.getComputedStyle(element);
        let result = Object.assign({
            type: 'body'
        }, tableParse.styleParse(style))
        if (element.children.length > 0) {
            result['children'] = tableParse.childrenParse(element.children)
        }
        return result;
    },
    rowParse: (element) => {
        let style = window.getComputedStyle(element);
        let result = Object.assign({
            type: 'row',
            offsetHeight: element.offsetHeight,
            offsetTop: element.offsetTop,
            offsetLeft: element.offsetLeft,
            offsetWidth: element.offsetWidth,
            src: element.src,
            row: element.rowspan
        }, tableParse.styleParse(style))
        if (element.children.length > 0) {
            result['children'] = tableParse.childrenParse(element.children)
        }
        return result;
    },
    colParse: (element) => {
        let style = window.getComputedStyle(element);
        let result = Object.assign({
            type: 'col',
            offsetHeight: element.offsetHeight,
            offsetTop: element.offsetTop,
            offsetLeft: element.offsetLeft,
            offsetWidth: element.offsetWidth,
            src: element.src,
            col: element.colspan,
            text: element.textContent
        }, tableParse.styleParse(style))
        if (element.children.length > 0) {
            result['children'] = tableParse.childrenParse(element.children)
        }
        return result;
    },
    imgParse: (element) => {
        let style = window.getComputedStyle(element);
        let result = Object.assign({
            type: 'img',
            offsetHeight: element.offsetHeight,
            offsetTop: element.offsetTop,
            offsetLeft: element.offsetLeft,
            offsetWidth: element.offsetWidth,
            src: element.src,
            alt: element.alt
        }, tableParse.styleParse(style))
        if (element.children.length > 0) {
            result['children'] = tableParse.childrenParse(element.children)
        }
        return result;
    },
    textParse: (element) => {
        let style = window.getComputedStyle(element);
        let result = Object.assign({
            type: 'text',
            offsetHeight: element.offsetHeight,
            offsetTop: element.offsetTop,
            offsetLeft: element.offsetLeft,
            offsetWidth: element.offsetWidth,
            src: element.src,
            alt: element.alt,
            text: element.textContent
        }, tableParse.styleParse(style))
        if (element.children.length > 0) {
            result['children'] = tableParse.childrenParse(element.children)
        }
        return result;
    },
    /**
     * 提取Style到Object
     * @param {CSSStyleDeclaration} style 
     */
    styleParse: (style) => {
        let result = {
            position: style.position,
            left: style.left,
            right: style.right,
            bottom: style.bottom,
            top: style.top,
            zIndex: style.zIndex,
            width: style.width,
            height: style.height,
            borderTopColor: style.borderTopColor,
            borderLeftColor: style.borderLeftColor,
            borderBottomColor: style.borderBottomColor,
            borderRightColor: style.borderRightColor,
            borderTopWidth: style.borderTopWidth,
            borderLeftWidth: style.borderLeftWidth,
            borderBottomWidth: style.borderBottomWidth,
            borderRightWidth: style.borderRightWidth,
            borderTopStyle: style.borderTopStyle,
            borderLeftStyle: style.borderLeftStyle,
            borderBottomStyle: style.borderBottomStyle,
            borderRightStyle: style.borderRightStyle,
            fontSize: style.fontSize,
            fontWeight: style.fontWeight,
            textAlign: style.textAlign,
            lineHeight: style.lineHeight,
            color: style.color,
            backgroundColor: style.backgroundColor
        }
        return result;
    }
}

export default tableParse;