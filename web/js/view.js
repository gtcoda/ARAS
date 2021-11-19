
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


};