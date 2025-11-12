

function oc3sengineDisablePointerEvents(classes = '') {
    let els = document.querySelectorAll(classes);
    let els_l = els.length;
    for (let i = 0; i < els_l; i++) {
        els[i].style.pointerEvents = 'none';//
    }
}



function oc3sengineLoadInstructions(element_selector,side,distance) {//side can have 
    //values: left , right; distance in pixels

    let oc3sedata = {'oc3se_gpt_loadnonce': oc3se_gpt_loadnonce};
    oc3sedata['action'] = 'oc3se_gpt_load_instruction';
    oc3sedata['instructions_per_page'] = document.querySelector('#instructions_per_page').value;
    oc3sedata['search'] = document.querySelector('#oc3sengine_search').value;
    oc3sedata['page'] = document.querySelector('#oc3sengine_page').value;
    oc3senginePutInstructionsLoader(element_selector,side,distance);
    oc3se_performAjax.call(oc3se_load_instruction_result, oc3sedata);

}


function oc3sengineShowPagination(data) {

    let total_instructions = data['total'];
    let page = data['page'];
    let items_per_page = data['items_per_page'];
    let prev_page = page - 1;

    let show_next = true;
    if ((page * items_per_page) >= +total_instructions) {
        show_next = false;
    }

    let show_prev = true;
    if (prev_page < 1) {
        show_prev = false;
    }
    let prev_page_as = document.querySelectorAll('.oc3seprevious.page-numbers');
    let next_page_as = document.querySelectorAll('.oc3senext.page-numbers');
    for (let i = 0; i < prev_page_as.length; i++) {
        prev_page_a = prev_page_as[i];
        prev_page_a.style.pointerEvents = '';//
        if (show_prev) {
            prev_page_a.style.display = 'inline-block';
        } else {
            prev_page_a.style.display = 'none';
        }
    }

    for (let i = 0; i < next_page_as.length; i++) {
        next_page_a = next_page_as[i];
        next_page_a.style.pointerEvents = '';//
        if (show_next) {

            next_page_a.style.display = 'inline-block';

        } else {
            next_page_a.style.display = 'none';
        }
    }



    let totals = document.querySelectorAll('.oc3sengine_total_instructions');
    for (let i = 0; i < totals.length; i++) {
        let total = totals[i];
        total.innerHTML = 'Total: ' + total_instructions + ' items';
    }
    let page_numbers = document.querySelectorAll('.page-numbers.current');
    for (let i = 0; i < page_numbers.length; i++) {
        let page_number = page_numbers[i];
        page_number.innerHTML = page;
    }
}



let oc3se_load_instruction_result = {

    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {
            return;
        }
        let pagidata = {'page': res.page, 'items_per_page': res.instructions_per_page, 'total': res.total};//instructions_per_page

        //update appropriate row in table
        oc3sengine_instructions = res.js_instructions;
        //oc3sengine_edited_instruction_id = 0;
        let tbody = document.querySelector('#oc3sengine-the-list');
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        let rows = '';
        for (let idx in oc3sengine_instructions) {
            let instruction_o = oc3sengine_instructions[idx];
            let tr = '<tr class="';
            if (instruction_o.disabled === '1') {
                tr += 'oc3sengine_disabled_text';
            }
            tr += '">';
            let td1 = '<td class="id_column">' + instruction_o.id + '</td>';
            let td2 = '<td><a href="#" onclick="oc3sengineEditInstruction(event,' + instruction_o.id + ' )" id="oc3sengine_instr_href_' + instruction_o.id + '">' + instruction_o.instruction + '</a></td>';
            let typeofinstr = '';
            if (instruction_o.typeof_instruction === '1') {
                typeofinstr = oc3sengine_text_edit_label;
            } else {
                typeofinstr = oc3sengine_code_edit_label;
            }
            let td3 = '<td class="mvertical"><span id="oc3sengine_type_instr_span_' + instruction_o.id + '">' + typeofinstr + '</span></td>';
            let disabled = '';
            if (instruction_o.disabled === '1') {
                disabled = oc3sengine_disabled_label;
            } else {
                disabled = oc3sengine_enabled_label;
            }
            let td4 = '<td class=""><span id="oc3sengine_enabled_span_' + instruction_o.id + '">' + disabled + '</span></td>';
            let td5 = '<td class="oc3sengine_user mvertical"><span>' + instruction_o.user_id + '</span></td>';
            let td6 = '<td class="oc3sengine_flags_td"><span title="edit" class="dashicons dashicons-edit" onclick="oc3sengineEditInstruction(event,' + instruction_o.id + ')"></span> ';
            if (instruction_o.disabled === '1') {
                td6 += '<span title="enable" class="dashicons dashicons-insert" onclick="oc3sengineToggleInstruction(event,' + instruction_o.id + ')"></span> ';
            } else {
                td6 += '<span title="disable" class="dashicons dashicons-remove" onclick="oc3sengineToggleInstruction(event,' + instruction_o.id + ')"></span> ';
            }
            td6 += '<span title="remove" class="dashicons dashicons-trash"  onclick="oc3sengineRemoveRow(event,' + instruction_o.id + ')"></span></td>';
            tr = tr + td1 + td2 + td3 + td4 + td5 + td6 + '</tr>';
            rows = rows + tr;
        }
        tbody.innerHTML = rows;

        oc3sengineShowPagination(pagidata);
        let totals = document.querySelectorAll('.oc3sengine_total_instructions');
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }

        let new_table_container_height = oc3sengineSetTableContainerHeight();//
        if (new_table_container_height > oc3sengine_instruction_table_height) {
            oc3sengine_instruction_table_height = new_table_container_height;
            let  tbl_div = document.querySelector('#oc3sengine_container');
            if (tbl_div) {
                tbl_div.style.height = oc3sengine_instruction_table_height + 'px';
            }
        }

    },

    ajaxComplete: oc3sengineHideLoader
};




/*config models and general*/

function oc3sengineSetTableContainerHeight() {
    let table_element = document.querySelector('#oc3sengine_instructions');
    if(!table_element){
        return;
    }
    let table_height = table_element.offsetHeight;
    let table_element2 = document.querySelector('#oc3sengine_search_submit');
    if(!table_element2){
        return;
    }
    let search_submit_h = table_element2.offsetHeight;
    let pagination = document.querySelector('.oc3sengine_pagination');
    let pl = 0;
    if (pagination) {
        pl = pagination.offsetHeight;
    }
    let container_height = table_height + search_submit_h + pl + 20;

    return container_height;
}


function oc3sengineSaveGeneral(e) {
    e.preventDefault();
    
    if(!jQuery){
        alert(oc3sengine_jquery_is_not_installed);
        return;
    }
    let indexsel = document.querySelector('#oc3sengine_config_pinecone_index');
    let indextitle = document.querySelector('#oc3sengine_config_pinecone_index_title');
    let idxx = indexsel.selectedIndex;
    console.log(idxx);
    let optt = indexsel.options[idxx];
    indextitle.value = optt.text;
    let genForm = jQuery('#oc3sengine_gen_form');
    let data = genForm.serialize();
    oc3senginePutGeneralLoader();
    oc3se_performAjax.call(oc3se_general_tab_dynamic, data);

}

function oc3sengineSaveSearch(e) {
    e.preventDefault();
    
    if(!jQuery){
        alert(oc3sengine_jquery_is_not_installed);
        return;
    }
    let genForm = jQuery('#oc3sengine_searchgen_form');
    let data = genForm.serialize();
    oc3senginePutGeneralLoader();
    oc3se_performAjax.call(oc3se_search_general_tab_dynamic, data);

}




function oc3senginePutModelsLoader(){
    let targetelement = document.querySelector('#oc3sengine_refresh_models');// event.target;
    let loader = document.querySelector('.oc3sengine-models-loader');
    
    if(targetelement && loader){
        let parentel = targetelement.parentElement.parentElement;
        let rect = targetelement.getBoundingClientRect();
        if(rect && parentel){
            let parentelrect = parentel.getBoundingClientRect();
            let rght = rect.right - parentelrect.left;
            loader.style.left = (rght+20) + 'px';
        }
    }
}

function oc3senginePutInstructionsLoader(element_selector,side,distance,loader_selector){

    let targetelement = document.querySelector(element_selector);// '.oc3sengine_button_container.oc3sengine_bloader'
    //let loader = document.querySelector('.oc3sengine-instructions-loader');
    let loader = null;
    if(loader_selector){
        loader = document.querySelector(loader_selector);
    }else{
        loader = document.querySelector('.oc3sengine-instructions-loader');
    }
    if(targetelement && loader){

        let rect = targetelement.getBoundingClientRect();
        let rght = rect.right - rect.left;
        console.log(rect);
        console.log(rght);
        if(rect){
            if(side === 'left'){
                loader.style.left = (Math.round(rect.left) - Math.round(distance)) +  'px';
                loader.style.top = (Math.round(rect.top) - 5) +  'px';
            }
            if(side === 'right'){
                loader.style.left = (Math.round(rect.right) + Math.round(distance)) +  'px';
                loader.style.top = (Math.round(rect.top) - 5) +  'px';
            }
            
        }
        //console.log('left = ' + loader.style.left);
        //console.log('top = ' + loader.style.top);
    }
}

function oc3senginePutGeneralLoader(){
    let targetelement = document.querySelector('.oc3sengine_gbutton_container.oc3sengine_bloader');// event.target;
    let loader = document.querySelector('.oc3sengine-general-loader');
    if(targetelement && loader){

        let rect = targetelement.getBoundingClientRect();
       
        if(rect){
            loader.style.left =  '200px';

        }
    }
}


function oc3sengineShowLoader(loader_selector){

    if(!jQuery){
        return;
        
    }

    if(typeof loader_selector === 'string'){
        jQuery(loader_selector).css('display','grid');
    }else{
        jQuery('.oc3sengine-custom-loader').css('display','grid');
    }
    
}

function oc3sengineHideLoader(){

    if(jQuery){
        jQuery('.oc3sengine-custom-loader').hide();
    }
}


let oc3se_general_tab_dynamic = {
    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {
        
        oc3se_alertResultMessage(res, oc3sengine_message_config_general_error, oc3sengine_message_config_general_succes1);
        
    },

    ajaxComplete: oc3sengineHideLoader
};

let oc3se_search_general_tab_dynamic = {
    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {
        
        oc3se_alertResultMessage(res, oc3sengine_message_config_general_error, oc3sengine_message_config_general_succes1);
        
    },

    ajaxComplete: oc3sengineHideLoader
};


function oc3se_alertResultMessage(res, default_error, default_success){
    if (!'result' in res || res.result != 200) {
            if( 'msg' in res){
                alert(res.msg);
            }else{
                alert(default_error);
            }
            return false;
        }
        alert(default_success);
}



    function oc3se_performAjax(data) {
        jQuery.ajax({
            url: oc3sengineajaxAction,
            type: 'POST',
            dataType: 'json',
            context: this,
            data: data,
            beforeSend: this.ajaxBefore,
            success: this.ajaxSuccess,
            complete: this.ajaxComplete
        });
    }



/* correction metabox */


function oc3sengineMetaSelectInstruction(e, instr_id) {
    e.preventDefault();

    oc3sengine_edited_instruction_id = instr_id;
    let instruction_element = document.querySelector('#oc3sengine_instruction');
    instruction_element.value = oc3sengine_instructions[instr_id]['instruction'];//
    instruction_element.scrollIntoView({behavior: "smooth"});

}



function oc3sengineMetaLoadInstructions() {

    let oc3sedata = {'oc3se_gpt_loadnoncec': oc3se_gpt_loadnoncec};
    oc3sedata['action'] = 'oc3se_gpt_load_correct_instruction';
    oc3sedata['instructions_per_page'] = document.querySelector('#instructions_per_page').value;
    oc3sedata['search'] = document.querySelector('#oc3sengine_search').value;
    oc3sedata['page'] = document.querySelector('#oc3sengine_page').value;
    oc3sedata['show_enabled_only'] = 1;
    oc3se_performAjax.call(oc3se_load_correct_instruction_result, oc3sedata);

}


let oc3se_load_correct_instruction_result = {

    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {
            return;
        }
        let pagidata = {'page': res.page, 'items_per_page': res.instructions_per_page, 'total': res.total};//instructions_per_page


        oc3sengine_instructions = res.js_instructions;
        let tbody = document.querySelector('#oc3sengine-the-list');
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        let rows = '';
        for (let idx in oc3sengine_instructions) {
            
            let instruction_o = oc3sengine_instructions[idx];
            let tr = '<tr class="';
            if (instruction_o.disabled === '1') {
                tr += 'oc3sengine_disabled_text';
            }
            tr += '">';
            let td1 = '<td class="id_column">' + '<a href="#" onclick="oc3sengineMetaSelectInstruction(event,' + instruction_o.id + ' )" >' + instruction_o.id + '</a></td>';
            let td2 = '<td><a href="#" onclick="oc3sengineMetaSelectInstruction(event,' + instruction_o.id + ' )" id="oc3sengine_instr_href_' + instruction_o.id + '">' + instruction_o.instruction + '</a></td>';
            let typeofinstr = '';
            if (instruction_o.typeof_instruction === '1') {
                typeofinstr = oc3sengine_typeofinstr_text;
            } else {
                typeofinstr = oc3sengine_typeofinstr_code;
            }
            let td3 = '<td class="mvertical">' + '<a href="#" onclick="oc3sengineMetaSelectInstruction(event,' + instruction_o.id + ' )" >' + typeofinstr + '</a></td>';

            let td4 = '';
            let td5 = '';
            let td6 = '';
            tr = tr + td1 + td2 + td3 + td4 + td5 + td6 + '</tr>';
            rows = rows + tr;
        }
        tbody.innerHTML = rows;

        oc3sengineShowPagination(pagidata);
        let totals = document.querySelectorAll('.oc3sengine_total_instructions');
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }


    },

    ajaxComplete: oc3sengineHideLoader
};








/*Metabox  Generate tab functions */


    
    let oc3sengine_radion_gen = 2;
    let oc3sengine_radion_lst2 = ['Deleted', 'User'];
    function oc3sengineAddField(plusElement) {

        let displayButton = document.querySelector("#oc3sengine_response_td");
        let tbody = document.querySelector('#oc3sengine_expert_tbody');

        let oc3seiaia_cur_role = 'User';
        // creating the div container.
        for (let i = oc3sengine_radion_gen - 1; i > 0; i--) {
            if (oc3sengine_radion_lst2[i] === 'User') {
                oc3seiaia_cur_role = 'Assistant';
                break;
            }
            if (oc3sengine_radion_lst2[i] === 'Assistant') {
                oc3seiaia_cur_role = 'User';
                break;
            }

        }

        let tr = document.createElement("tr");
        tr.setAttribute("class", "oc3sengine_field");

        let td = document.createElement("td");
        td.setAttribute("colspan", "3");
        // Creating the textarea element.

        let radiodiv = document.createElement("div");

        radiodiv.setAttribute("class", "oc3sengine_halfscreen");

        let radiolbl1 = document.createElement("label");
        radiolbl1.innerHTML = oc3sengine_generate_assistant;

        let radioel1 = document.createElement("input");
        radioel1.setAttribute("type", "radio");
        radioel1.setAttribute("class", "oc3sengine_act");
        radioel1.setAttribute("name", "oc3sengine_actor" + oc3sengine_radion_gen);
        radioel1.setAttribute("id", "oc3sengine_actor_ae_" + oc3sengine_radion_gen);
        radioel1.setAttribute("id_gen_val", oc3sengine_radion_gen);
        radioel1.setAttribute("value", "Assistant");
        radioel1.setAttribute("onchange", "oc3sengineRadioChange(" + oc3sengine_radion_gen + ", 'ae')");

        if (oc3seiaia_cur_role === 'Assistant') {
            radioel1.setAttribute("checked", true);
        }
        radiolbl1.setAttribute("for", "oc3sengine_actor_ae_" + oc3sengine_radion_gen);

        let radiodiv2 = document.createElement("div");
        radiodiv2.setAttribute("class", "oc3sengine_halfscreen");
        let radiolbl2 = document.createElement("label");
        radiolbl2.innerHTML = oc3sengine_generate_user;
        let radioel2 = document.createElement("input");
        radioel2.setAttribute("type", "radio");
        radioel2.setAttribute("class", "oc3sengine_act");
        radioel2.setAttribute("name", "oc3sengine_actor" + oc3sengine_radion_gen);
        radioel2.setAttribute("id", "oc3sengine_actor_ue_" + oc3sengine_radion_gen);
        radioel2.setAttribute("id_gen_val", oc3sengine_radion_gen);
        radioel2.setAttribute("value", "User");
        radioel2.setAttribute("onchange", "oc3sengineRadioChange(" + oc3sengine_radion_gen + ", 'ue')");

        if (oc3seiaia_cur_role === 'User') {
            radioel2.setAttribute("checked", true);
        }
        radiolbl2.setAttribute("for", "oc3sengine_actor_ue_" + oc3sengine_radion_gen);

        let textareadiv = document.createElement("div");//
        textareadiv.setAttribute("class", "oc3sengine_2actor");

        let textarea = document.createElement("textarea");

        textarea.setAttribute("name", "oc3sengine_message_e_" + oc3sengine_radion_gen);
        textarea.setAttribute("id", "oc3sengine_message_e_" + oc3sengine_radion_gen);

        // Creating the textarea element.

        let plusminusdiv = document.createElement("div");//
        plusminusdiv.setAttribute("class", "oc3sengine_actor");
        // Creating the plus span element.
        let plus = document.createElement("span");
        plus.setAttribute("onclick", "oc3sengineAddField(this)");
        let plusText = document.createTextNode("+");
        plus.appendChild(plusText);

        // Creating the minus span element.
        let minus = document.createElement("span");
        minus.setAttribute("onclick", "oc3sengineRemoveField(this," + oc3sengine_radion_gen + ")");
        let minusText = document.createTextNode("-");
        minus.appendChild(minusText);

        // Adding the elements to the DOM.
        tbody.insertBefore(tr, displayButton);
        tr.appendChild(td);


        radiodiv.appendChild(radioel1);
        radiodiv.appendChild(radiolbl1);
        td.appendChild(radiodiv);


        radiodiv2.appendChild(radioel2);
        radiodiv2.appendChild(radiolbl2);
        td.appendChild(radiodiv2);

        textareadiv.appendChild(textarea);
        td.appendChild(textareadiv);

        plusminusdiv.appendChild(plus);
        plusminusdiv.appendChild(minus);
        td.appendChild(plusminusdiv);




        // Un hiding the minus sign.
        plusElement.nextElementSibling.style.display = "inline-block"; // the minus sign
        // Hiding the plus sign.
        plusElement.style.display = "none"; // the plus sign

        oc3sengine_radion_lst2[oc3sengine_radion_gen] = oc3seiaia_cur_role;
        oc3sengine_radion_gen += 1;
    }

    function oc3sengineRadioChange(gen_id, suffix) {

        let el_id = 'oc3sengine_actor_' + suffix + '_' + gen_id;
        let el_clicked = document.querySelector('#' + el_id);
        if (el_clicked) {
            let e_c_value = el_clicked.value;
            console.log(e_c_value);
            oc3sengine_radion_lst2[gen_id] = e_c_value;
            console.log(oc3sengine_radion_lst2);

        }

    }

    function oc3sengineRemoveField(minusElement, rmidx) {
        minusElement.parentElement.parentElement.parentElement.remove();
        oc3sengine_radion_lst2[rmidx] = 'Deleted';
        console.log(oc3sengine_radion_lst2);
    }


/* config chatbot*/
/*general tab*/

/*styles tab*/

function oc3senginePutChatbotStylesLoader(){
    let targetelement = document.querySelector('.oc3sengine_gbutton_container.oc3sengine_bloader');// event.target;
    let loader = document.querySelector('.oc3sengine-general-loader');
    if(targetelement && loader){

        let rect = targetelement.getBoundingClientRect();
       
        if(rect){
            loader.style.left =  '200px';

        }
    }
}

let oc3se_chatbot_styles_tab_dynamic = {
    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {
        
        oc3se_alertResultMessage(res, oc3sengine_message_config_styles_error, oc3sengine_message_config_styles_succes1);
        
    },

    ajaxComplete: oc3sengineHideLoader
};

//Working with bot log records
let oc3se_log_page_id = '#oc3sengine_page_log';
let oc3se_logs_per_page_id = '#logs_per_page';

let oc3se_toggle_selectedlog_result_dynamic = {
    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {

            return;

        }
        //update appropriate row in table
        let new_selection = res.new_selection;
        let modified_instruction_id = new_selection.id;
        let modified_instruction_id_str = modified_instruction_id + '';


        let oc3sengine_type_instr_disabled = document.querySelector('#oc3sengine_selected_span_' + modified_instruction_id);
        if (!oc3sengine_type_instr_disabled) {
            return;
        }
        let oc3sengine_instr_href = document.querySelector('#oc3sengine_selected_href_' + modified_instruction_id);
        if (!oc3sengine_instr_href) {
            return;
        }
        oc3sengine_log_infos[modified_instruction_id_str]['selected'] = new_selection.selected;

        let oc3sengine_tr = oc3sengine_instr_href.parentElement.parentElement;

        if (new_selection.selected === 1) {
            oc3sengine_type_instr_disabled.innerHTML = 'selected';
            oc3sengine_tr.classList.add("oc3sengine_selected_text");
            let insert_icon = oc3sengine_tr.querySelector('span.dashicons-insert');
            if (insert_icon) {
                insert_icon.classList.add('dashicons-remove');
                insert_icon.classList.remove('dashicons-insert');
            }
            
        } else {
            oc3sengine_type_instr_disabled.innerHTML = '';
            oc3sengine_tr.classList.remove("oc3sengine_selected_text");
            let remove_icon = oc3sengine_tr.querySelector('span.dashicons-remove');
            if (remove_icon) {
                remove_icon.classList.add('dashicons-insert');
                remove_icon.classList.remove('dashicons-remove');
            }
        }
    },

    ajaxComplete: oc3sengineHideLoader
};


function oc3sengineLoadLogsE(e) {
    e.preventDefault();
    document.querySelector(oc3se_log_page_id).value = 0;
    oc3sengineLoadLogs('#oc3sengine_container','right',-70);
}

function oc3sengineLoadLogs(element_selector,side,distance) {//side can have 
    //values: left , right; distance in pixels

    let oc3sedata = {'oc3se_gpt_loadnonce': oc3se_gpt_loadnonce};
    oc3sedata['action'] = 'oc3se_gpt_load_log';
    oc3sedata['logs_per_page'] = document.querySelector(oc3se_logs_per_page_id).value;
    oc3sedata['search'] = document.querySelector('#oc3sengine_search').value;
    oc3sedata['page'] = document.querySelector(oc3se_log_page_id).value;
    oc3senginePutInstructionsLoader(element_selector,side,distance);
    oc3se_performAjax.call(oc3se_load_log_result, oc3sedata);

}


let oc3se_load_log_result = {

    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {
            return;
        }
        let pagidata = {'page': res.page, 'items_per_page': res.logs_per_page, 'total': res.total};//

        //update appropriate row in table
        let oc3sengine_logs = res.log_records;

        let tbody = document.querySelector('#oc3sengine-the-list');
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        let rows = '';
        for (let idx in oc3sengine_logs) {
            let log_o = oc3sengine_logs[idx];
            let tr = '<tr class="';
            if (log_o.selected === '1') {
                tr += 'oc3sengine_selected_text';
            }
            tr += '">';
            let td1 = '<td class="id_column">' + log_o.id + '</td>';
            let td2 = '<td><a href="#" onclick="oc3sengineShowRecord(event,' + log_o.id + ' )" id="oc3sengine_selected_href_' + log_o.id + '">' + log_o.preview + '</a></td>';
          
            let td3 = '<td class="mvertical"><span id="oc3sengine_type_instr_span_' + log_o.id + '">' + log_o.visitor_info + '</span></td>';
            
            let td4 = '<td class=""><span id="oc3sengine_selected_span_' + log_o.id + '">' + log_o.chat_info + '</span></td>';
            let td5 = '<td class="oc3sengine_user mvertical"><span>' + log_o.created + '</span></td>';
            let td6 = '<td class="oc3sengine_flags_td"> ';//<span title="edit" class="dashicons dashicons-edit" onclick="oc3sengineEditInstruction(event,' + log_o.id + ')"></span>
            if (log_o.selected === '1') {
                td6 += '<span title="unselect" class="dashicons dashicons-remove" onclick="oc3sengineLogSelectRow(event,' + log_o.id + ')"></span> ';
            } else {
                td6 += '<span title="select" class="dashicons dashicons-insert" onclick="oc3sengineLogSelectRow(event,' + log_o.id + ')"></span> ';
            }
            td6 += '<span title="remove" class="dashicons dashicons-trash"  onclick="oc3sengineLogsRemoveRow(event,' + log_o.id + ')"></span></td>';
            tr = tr + td1 + td2 + td3 + td4 + td5 + td6 + '</tr>';
            rows = rows + tr;
        }
        tbody.innerHTML = rows;

        oc3sengineShowPagination(pagidata);
        let totals = document.querySelectorAll('.oc3sengine_total_instructions');
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }

        let new_table_container_height = oc3sengineSetTableContainerHeight();//
        if (new_table_container_height > oc3sengine_instruction_table_height) {
            oc3sengine_instruction_table_height = new_table_container_height;
            let  tbl_div = document.querySelector('#oc3sengine_container');
            if (tbl_div) {
                tbl_div.style.height = oc3sengine_instruction_table_height + 'px';
            }
        }
        oc3sengine_log_messages = res.js_logmessages;
        oc3sengine_log_infos = res.js_loginfos;
        
        let oc3se_thread_info = document.querySelector('#oc3se_bot_thread_info');
        let oc3se_thread_history = document.querySelector('#oc3se_bot_thread_history');
        oc3se_thread_info.innerHTML = '<div class="oc3se-history" style="display: flex; flex-direction: column; margin-bottom: 5px;"><span>No data</span></div>';
        oc3se_thread_history.innerHTML = '<div class="oc3se-history" style="display: flex; flex-direction: column; margin-bottom: 5px;"><span>No data</span></div>';
        
    },

    ajaxComplete: oc3sengineHideLoader
};


let oc3se_delete_log = {

    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res ) {
            return;
        }
        
        if(res.result === 10){
            alert(res.msg + ' '+'only admin has acces to this operation');
            return;
        }
        
        let del_log = res.del_log;

        let link_to_delete = document.querySelector('#oc3sengine_selected_href_' + del_log);
        if (link_to_delete) {
            link_to_delete.parentElement.parentElement.remove();
        }
        delete oc3sengine_log_messages[del_log];
        delete oc3sengine_log_infos[del_log];
        oc3sengineLoadLogs('#oc3sengine_container','left',-70);

        //oc3sengineLoadInstructions('.oc3sengine_button_container.oc3sengine_bloader');

    },

    ajaxComplete: oc3sengineHideLoader
};





function oc3sengineLogsRemoveRow(e, log_id) {

    e.preventDefault();
    let wantremove = confirm(oc3se_message_log_confirm_delete + ':' + log_id);
    if (!wantremove) {
        return 0;
    }

    //check input fields before store instruction
    let oc3sedata = {'oc3se_bot_dellognonce': oc3se_bot_dellognonce};
    oc3sedata['action'] = 'oc3se_gpt_delete_log';
    oc3sedata['id'] = log_id;
    document.querySelector('#oc3sengine_page_log').value = 1;
    oc3senginePutInstructionsLoader('#oc3sengine_container','left',-70);
    oc3se_performAjax.call(oc3se_delete_log, oc3sedata);

}


function oc3sengineLogSelectRow(e, instr_id) {
    e.preventDefault();

    //check input fields before store instruction
    let oc3sedata = {'oc3se_gpt_toggleselectionnonce': oc3sengine_toggleselectionnonce};
    oc3sedata['action'] = 'oc3se_gpt_toggle_selectionlog';
    oc3sedata['id'] = instr_id;



    oc3se_performAjax.call(oc3se_toggle_selectedlog_result_dynamic, oc3sedata);

}



let oc3se_changemode_log = {

    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'cd' in res ) {
            return;
        }
        console.log(res);
        let oc3sengine_turn_log = document.querySelector('#oc3sengine_turn_log');
        if(!oc3sengine_turn_log ){
            return;
        }
        
        if(res.cd == 1){
            oc3sengine_turn_log.innerHTML = oc3se_bot_turnon_msg;
        }else{
            oc3sengine_turn_log.innerHTML = oc3se_bot_turnoff_msg;
        }

    },

    ajaxComplete: oc3sengineHideLoader
};



let oc3se_bot_logtext = {

    ajaxBefore: oc3sengineShowLoader,

    ajaxSuccess: function (res) {

        if (!'cd' in res ) {
            return;
        }
        console.log(res);
    },

    ajaxComplete: oc3sengineHideLoader
};


function oc3copyToClipboardInnerHtml(e,elementid,copy_clipboard_sucess,copy_clipboard_fail){
        e.preventDefault();
        let tarea = document.querySelector('#'+elementid);
        if(!tarea){
            return;
        }
        let lxt = tarea.innerHTML;
        if (!navigator.clipboard) {
            return;
        }

        navigator.clipboard.writeText(lxt).then(function() {
        alert(copy_clipboard_sucess);
      }, function(err) {
        alert(copy_clipboard_fail + ': '+ err);
      });

    }