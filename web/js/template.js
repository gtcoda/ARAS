// Управление загрузкой шаблонов
import * as settings from './settings.js';

var template = new Map();


 
export default {
    getTemplate(templateName) {


        if(!template.has(templateName)){

            template.set(templateName,false);
            var templateUrl = "templates/" + templateName + "Template.hbs";
    
            $.ajax({
                url: templateUrl,
                async: false,
                success: function (data) {
                    $("#" + settings.getTemplateApp()).append(data);
                    template.set(templateName,true);
                    console.log(template);
                }
            });
        }


        console.log(template);

    },
        

      
}


