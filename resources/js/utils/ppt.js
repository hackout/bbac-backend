
import PptxGenJS from "pptxgenjs";

let pres;
let slide;
//A4 尺寸
let height = 5.62598425197
let width = 10
let type = 'vehicle'
let titles = {
    vehicle: {
        big: 'QM-E&B',
        title: 'I-Service'
    },
    product: {
        big: 'QM-E&B',
        title: 'Product Audit'
    },
    inline: {
        big: 'QM-E&B',
        title: 'Inline Audit'
    }
}

let ppt = {
    pres: new PptxGenJS(),
    init: (t = 'vehicle',layout = 'LAYOUT_16x9') => {
        pres = new PptxGenJS()
        pres.layout = layout
        slide = pres.addSlide()
        type = t
        return ppt
    },
    addSlide: () => {
        slide = pres.addSlide()
    },
    addCircle: t => {
        slide.addShape(pres.shapes.OVAL, t)
    },
    addSquare: t => {
        slide.addShape(pres.ShapeType.rect, t)
    },
    addText: (val, t) => {
        slide.addText(val, t)
    },
    addImage: t => {
        slide.addImage(t)
    },
    addShape: t => {
        slide.addShape(pres.ShapeType.rect, t)
    },
    addBlock: (val, t) => {
        slide.addShape(pres.ShapeType.rect, {
            fill: {
                color: '2a426c'
            },
            h: 0.31496063,
            w: t.w,
            x: t.x,
            y: t.y
        })
        slide.addShape(pres.ShapeType.rect, {
            fill: {
                color: '27406A',
                type: 'solid'
            },
            h: 0.157480315,
            w: t.w,
            x: t.x,
            y: t.y
        })
        slide.addShape(pres.shapes.ROUNDED_RECTANGLE, {
            fill: {
                color: 'fee192'
            },
            rectRadius: 1,
            h: 0.157480315,
            w: 0.05,
            x: t.x + 0.2,
            y: t.y + 0.085
        })
        slide.addText(val, {
            color: 'FFFFFF',
            fontSize: 12,
            w: t.w,
            y: t.y + 0.14,
            x: t.x + 0.235,
            valign: 'middle'
        })
    },
    title: title => {
        slide.addShape(pres.ShapeType.rect, {
            fill: {
                color: "27406A"
            },
            y: 0,
            x: 0,
            w: '100%',
            h: 1 / 2 * 0.96
        })
        slide.addImage({
            path: '/assets/imgs/report_arrow.png',
            w: 0.2598425,
            h: 0.2283465,
            y: 0.1259843,
            x: 0.1259843,
        })
        slide.addText(title, {
            y: 0.1968504,
            x: 0.4606299,
            color: "FFFFFF",
            fontSize: 16,
            fill: {
                color: "27406A"
            },
        })
        slide.addImage({
            path: '/assets/imgs/report_icon.png',
            w: 3.9062,
            h: 1 / 2 * 0.96,
            x: width - 3.9062,
            y: 0,
        })
        slide.addText(titles[type].big, {
            x: 7.4133858,
            y: 0,
            w: 1,
            h: 0.256,
            color: "27406A",
            fontSize: 12,
            align: "center"
        })
        slide.addText(titles[type].title, {
            x: 7.4133858,
            y: 0.1850394,
            w: 1,
            h: 0.256,
            color: "27406A",
            fontSize: 10,
            align: "center"
        })
    },
    table: (rows = [], extra = {}) => {
        slide.addTable(rows, extra)
    },
    item: (title, titleStyle = {}, pictureStyle = {}) => {
        slide.addText(title, titleStyle)
        slide.addImage(pictureStyle)
    },
    footer: page => {
        let xH = 1 / 3 * 0.96;
        slide.addText(page, {
            fill: {
                color: "e2e4e9"
            },
            color: '27406A',
            y: height - xH,
            x: 0,
            w: '100%',
            h: xH,
            align: 'center',
            valign: 'middle',
            fontSize: '6'
        })
    },
    save: () => {
        pres.writeFile()
    }
}

export default ppt;