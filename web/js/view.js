
// Обработчик для сравнения в IF/ELSE
Handlebars.registerHelper('ifEq', function (a, b, obj) {
    if (a == b) {
        return obj.fn(this);
    } else {
        return obj.inverse(this);
    }

});



// Обработчик для вставики текущей даты
Handlebars.registerHelper('Date', function () {

    var options = {
        month: 'long',
        day: 'numeric',
        timezone: 'UTC'
    };
    var d = new Date().toLocaleString("ru", options);

    return new Handlebars.SafeString(`${d}`);
});







export default {
    render(templateName, data = {}) {

        templateName = templateName + 'Template';
        const templateElement = document.getElementById(templateName);


        if (templateElement) {
            const templateSource = templateElement.innerHTML;
            const renderFn = Handlebars.compile(templateSource);
            return renderFn(data);
        }
        else {
            console.log(' Нет шаблона: ');
            console.log(templateName);
            alert(' Нет шаблона: ');

        }

    },

    // Функция вставики фрейма с вики
    // divwiki - id элемента для вставки
    // src - 
    wiki(divwiki, src) {
        let myIframe = document.createElement('iframe');
        console.log();

        myIframe.src = src;
        myIframe.width = `100%`;
        myIframe.id = `frame`;
        myIframe.setAttribute(`scrolling`, `no`);
        document.getElementById(divwiki).append(myIframe);

        // Вешаем обработчик события onload на наш элемент iframe, который лежит в myIframe
        myIframe.onload = () => {

            let interval = setInterval(function () {

                if (myIframe.contentWindow != null) {
                    myIframe.height = myIframe.contentWindow.document.body.scrollHeight;
                }

            }, 1000);


        }
    }

};