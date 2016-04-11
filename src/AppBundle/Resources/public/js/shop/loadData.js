$(document).ready(function() {
    $.getJSON('/shop/data', function (data) {
        console.log(data);
        makeGrid(data);
    });
});

function makeGrid(jsondata) {
    $('#grid').jqGrid({
        data: jsondata,
        datatype: 'local',
        jsonReader: {repeatitems: false },
        colNames:['Название','Картинка','Дата выхода','Разработчики','Сайт','Цена'],
        colModel :[
            { name:'name', width:150},
            { name:'headerImage', width:153, height:71, formatter: function (val) {
                return jQuery('<img/>', { src: val, height: '71px', width: '153px', alt: 'Нет картинки' })[0].outerHTML;
            } },
            { name:'releaseDate', width:100 },
            { name:'developers', width:100, formatter: function (val) {
                var cell = $('<div/>').html(val);
                if (val == 'Valve')
                    cell.addClass('volvored');
                return cell[0].outerHTML;
            } },
            { name:'website', width:350, formatter: function (val) {
                return (val != '{NO_WEBSITE}') ? $('<a/>', {
                        href: val, target: '_blank'
                    }).html(/http:\/\/([^ \f\n\r\t\v\/]+)/.exec(val)[1])[0].outerHTML : 'Неизвестно';
            }},
            { name:'price', width:80 }
        ],
        pager: '#gridpager',
        rowNum:6,
        rowList:[6,12,18,24,30],
        sortname: 'name',
        height: 'auto',
        width: 'auto',
        caption: 'Игры Steam'
    }).navGrid('#gridpager', {
        add: false,
        del: false,
        edit: false,
        view: false,
        search: false
    });
}
