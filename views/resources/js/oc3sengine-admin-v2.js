let oc3sengineRowsOptions = {
    row_Loadnonce: 'variable oc3wp_gpt_loadnoncec',
    selected_Span: '#oc3sengine_selected_span_',
    selected_Href:'#oc3sengine_selected_href_',
    table_ElementId:'#oc3sengine_instructions',
    search_SubmitElementId:'#oc3sengine_search_submit',
    message_LogConfirmDelete:'',/*oc3wp_message_log_confirm_delete,*/
    row_DellogNonce:'',/**/
    row_Href_Prefix:'',
    row_PageId:'#oc3sengine_page_log',
    rows_PerPageId:'#logs_per_page',
    rows_ContainerId:'#oc3sengine_container',
    search_InputId:'#oc3sengine_search',
    load_RowsAction:'oc3wp_gpt_load_log',
    delete_RowsAction:'oc3wp_gpt_delete_log',
    loader_Selector: null
};



//typeof oc3sengineRowsOptions === 'object' && !Array.isArray(oc3sengineRowsOptions) && oc3sengineRowsOptions !== null


class Oc3SengineRowsManager {

    constructor(options) {
        this.rowPageId = options.row_PageId;//'#oc3sengine_page_log';
        this.rowsPerPageId = options.rows_PerPageId;//'#logs_per_page';
        this.rowsContainerId = options.rows_ContainerId;//'#oc3sengine_container';
        this.searchInputId = options.search_InputId;//'#oc3sengine_search';
        this.loadRowsAction = options.load_RowsAction;//'oc3wp_gpt_load_log';
        this.deleteRowsAction = options.delete_RowsAction;//'oc3wp_gpt_delete_log';
        
        this.rowLoadnonce = options.row_Loadnonce;//variable oc3wp_gpt_loadnoncec
        this.selectedRowSpan = options.selected_Span;//#oc3sengine_selected_span_'
        this.selectedRowHref = options.selected_Href;//'#oc3sengine_selected_href_'
        this.tableElementId = options.table_ElementId;//this is because many table elements can be in one page //'#oc3sengine_instructions'
        this.searchSubmitElementId = options.search_SubmitElementId;// '#oc3sengine_search_submit'
        this.messageLogConfirmDelete = options.message_LogConfirmDelete; //oc3wp_message_log_confirm_delete
        this.rowDellogNonce = options.row_DellogNonce;//oc3wp_bot_dellognonce
        this.ajaxAction = options.ajax_Action;//oc3wp_bot_dellognonce
        this.tableRowHrefPrefix = options.table_Row_Href_Prefix;
        this.messageUpdateSuccess = options.message_Update_Success;
        this.messageNewSuccess = options.message_New_Success;
        this.tableContainer = options.table_Container;
        this.pageNumbers = options.page_Numbers;
        this.totalRows = options.total_Rows;
        this.rowItems = options.row_items;
        this.appSuffix = options.app_suffix;

        this.loaderSelector = options.loader_Selector;
        this.sourceRowsProp = options.source_RowsProp;
        this.tbodyElement = options.tbody_Element;
        //'#oc3sengine_selected_href_' + modifiedInstructionId);
        
        this.oc3wpRows = {};
        this.oc3sengineRowInfos = {};
        this.oc3sengineRowMessages = {};

        this.initialize();
    }

    initialize() {
        document.querySelector(this.rowPageId).value = 0;
    }

    showLoader(loaderSelector) {
        if (!jQuery) {
            return;

        }

        if (typeof loaderSelector === 'string') {
            jQuery(loaderSelector).css('display', 'grid');
        } else {
            if(typeof this.loaderSelector === 'string'){
                jQuery(this.loaderSelector).css('display', 'grid');
            }else{
                jQuery('.oc3sengine-custom-loader').css('display', 'grid');
            }
        }
    }

    hideLoader() {
        if (jQuery) {
            jQuery('.oc3sengine-custom-loader').hide();
        }
    }

    toggleSelectedRow(oc3wpdata) {
        this.performRequestCall(this.toggleSelectedRowCallbacks(), oc3wpdata);
    }

    loadRows(elementSelector, side, distance,successcallback) {//oc3sengineLoadLogs
        const oc3wpdata = this.collectRowData();
        this.putLoader(elementSelector, side, distance);
        this.performRequestCall(this.loadRowCallbacks(successcallback), oc3wpdata);
    }

    deleteRow(oc3wpdata) {
        oc3wpdata.sendAjax = 1;
        oc3wpdata.sendJson = 1;
        this.performRequestCall(this.deleteRowCallbacks(), oc3wpdata);
    }

    collectRowData() {
        return {
            loadnonce: this.rowLoadnonce,
            action: this.loadRowsAction,
            rows_per_page: document.querySelector(this.rowsPerPageId).value,
            search: document.querySelector(this.searchInputId).value,
            page: document.querySelector(this.rowPageId).value,
            sendAjax:1
        };
    }

    ajaxSuccessUpdateRow(res) {//Object specific. Override in child classes.

        console.log(res);
        

        if (!('result' in res) || res.result != 200) {
            if('msg' in res){
                this.displayAlert(res.msg);
            }
            return;
        }
        
        if(!(this.sourceRowsProp in res )){
            return;
        }
        
        let srows = this.sourceRowsProp;
        this.rowItems = res[srows];//.source_rows;
        let pagidata = {'page': res.page, 'items_per_page': res.rows_per_page, 'total': res.total};

        let tbody = document.querySelector('#'+this.tbodyElement + this.appSuffix);
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        

        let rows = this.populateTBody();
        tbody.innerHTML = rows;

        this.showPagination(pagidata);
        let totals = document.querySelectorAll(this.totalRows);
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }

        
        
    }

    putLoader(elementSelector, side, distance, loaderSelector) {
        let targetelement = document.querySelector(elementSelector);// '.oc3sengine_button_container.oc3sengine_bloader'
        //let loader = document.querySelector('.oc3sengine-instructions-loader');
        let loader = null;
        if (loaderSelector) {
            loader = document.querySelector(loaderSelector);
        } else {
            loader = document.querySelector('.oc3sengine-instructions-loader');
        }
        if (targetelement && loader) {

            let rect = targetelement.getBoundingClientRect();
            let rght = rect.right - rect.left;
            console.log(rect);
            console.log(rght);
            if (rect) {
                if (side === 'left') {
                    loader.style.left = (Math.round(rect.left) - Math.round(distance)) + 'px';
                    loader.style.top = (Math.round(rect.top) - 5) + 'px';
                }
                if (side === 'right') {
                    loader.style.left = (Math.round(rect.right) + Math.round(distance)) + 'px';
                    loader.style.top = (Math.round(rect.top) - 5) + 'px';
                }

            }
            //console.log('left = ' + loader.style.left);
            //console.log('top = ' + loader.style.top);
        }

    }

    performRequestCall(callbacks, data) {
        //callbackFn = callbacks;
        this.showLoader();
        if(typeof data === 'object' && !Array.isArray(data) && data !== null && 'sendAjax' in data && data.sendAjax === 1){
            this.performAjax(callbacks, data);
        }else{
            this.performFetch(callbacks, data);
        }
    }
    
    performAjax(callbacks,data){
        jQuery.ajax({
            url: this.ajaxAction,
            type: 'POST',
            dataType: 'json',
            context: this,
            data: data,
            beforeSend: null,
            success: callbacks.ajaxSuccess,
            complete: callbacks.ajaxComplete
        });
    }
    
    performFetch(callbacks, data){
        let bdy = null;
        if(typeof data === 'object' && !Array.isArray(data) && data !== null && 'sendRaw' in data && data.sendRaw === 1){
            bdy = data;
        }else{
            bdy = JSON.stringify(data);
        }
        let hdrs = {};
        if(typeof data === 'object' && !Array.isArray(data) && data !== null && 'sendJson' in data && data.sendJson === 1){
            hdrs = {'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };
        }else{
            hdrs = {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json, text/javascript, */*; q=0.01'
            };
        }
        fetch(this.ajaxAction, {
            method: 'POST',
            body: bdy,
            headers: hdrs
        })
        .then(response => response.json())
        .then(callbacks.ajaxSuccess)
        .finally(callbacks.ajaxComplete);
    }

    toggleSelectedRowCallbacks() {
        return {
            ajaxBefore: this.showLoader,
            ajaxSuccess: res => this.ajaxSuccessUpdateRow(res),
            ajaxComplete: this.hideLoader
        };
    }

    loadRowCallbacks(successcallback) {
        return {
            ajaxBefore: this.showLoader,
            ajaxSuccess: res => successcallback(res),
            ajaxComplete: this.hideLoader
        };
    }

    deleteRowCallbacks() {
        return {
            ajaxBefore: this.showLoader,
            ajaxSuccess: res => this.ajaxSuccessUpdateRow(res),
            ajaxComplete: this.hideLoader
        };
    }


    loadRowsE(e) {//oc3sengineLoadLogsE
        e.preventDefault();
        document.querySelector(this.rowPageId).value = 1;
        let boundajaxSuccessUpdateRow = this.ajaxSuccessUpdateRow.bind(this);
        this.loadRows(this.rowsContainerId,'right', -70,boundajaxSuccessUpdateRow);
    }

    nextRowPage(e) {
        e.preventDefault();
        let currentPage = document.querySelector(this.rowPageId).value;
        document.querySelector(this.rowPageId).value = (+currentPage) + 1;
        this.disablePointerEvents('.oc3wpnext'+ this.pageNumbers);
        let boundajaxSuccessUpdateRow = this.ajaxSuccessUpdateRow.bind(this);
        this.loadRows(this.rowsContainerId,'left', -70,boundajaxSuccessUpdateRow);
    }

    changeRowPerPage(el) {
        document.querySelector(this.rowPageId).value = 1;
        let boundajaxSuccessUpdateRow = this.ajaxSuccessUpdateRow.bind(this);
        this.loadRows(this.rowsContainerId, 'left', -70,boundajaxSuccessUpdateRow);
    }

    prevRowPage(e) {
        e.preventDefault();
        let currentPage = document.querySelector(this.rowPageId).value;
        document.querySelector(this.rowPageId).value = (+currentPage) - 1;
        this.disablePointerEvents('.oc3wpprevious'+ this.pageNumbers);
        let boundajaxSuccessUpdateRow = this.ajaxSuccessUpdateRow.bind(this);
        this.loadRows(this.rowsContainerId, 'left', -70,boundajaxSuccessUpdateRow);
    }

    searchRowKeyUp(e) {
        e.preventDefault();
        if (e.key === 'Enter' || e.keyCode === 13) {
            document.querySelector(this.rowPageId).value = 0;
            let boundajaxSuccessUpdateRow = this.ajaxSuccessUpdateRow.bind(this);
            this.loadRows('.oc3sengine_button_container.oc3sengine_bloader','left', -70,boundajaxSuccessUpdateRow);
        }
    }

    clearSearch(e) {
        e.preventDefault();
        let search = document.querySelector(this.searchInputId);
        if (search) {
            search.value = '';
            document.querySelector(this.rowPageId).value = 1;
            let boundajaxSuccessUpdateRow = this.ajaxSuccessUpdateRow.bind(this);
            this.loadRows(this.rowsContainerId,'right', -70,boundajaxSuccessUpdateRow);
        }
    }
    
    removeRow(e, row_id) {
        e.preventDefault();
        let wantRemove = confirm(this.messageLogConfirmDelete + ':' + row_id);
        if (!wantRemove) return;

        let oc3wpdata = {
            oc3se_row_dellognonce: this.rowDellogNonce,
            action: this.deleteRowsAction,
            id: row_id
        };
        document.querySelector(this.rowPageId).value = 1;
        this.putLoader(this.rowsContainerId, 'left', -70);
        this.deleteRow(oc3wpdata);
    }

    
    
    disablePointerEvents(classes = '') {
        let els = document.querySelectorAll(classes);
        let els_l = els.length;
        for (let i = 0; i < els_l; i++) {
            els[i].style.pointerEvents = 'none';//
        }
    }

    showPagination(data) {//
        
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
        let prev_page_as = document.querySelectorAll('.oc3seprevious'+ this.pageNumbers);
        let next_page_as = document.querySelectorAll('.oc3senext'+ this.pageNumbers);
        for (let i = 0; i < prev_page_as.length; i++) {
            let prev_page_a = prev_page_as[i];
            prev_page_a.style.pointerEvents = '';//
            if (show_prev) {
                prev_page_a.style.display = 'inline-block';
            } else {
                prev_page_a.style.display = 'none';
            }
        }

        for (let i = 0; i < next_page_as.length; i++) {
            let next_page_a = next_page_as[i];
            next_page_a.style.pointerEvents = '';//
            if (show_next) {

                next_page_a.style.display = 'inline-block';

            } else {
                next_page_a.style.display = 'none';
            }
        }



        let totals = document.querySelectorAll(this.totalRows);
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + total_instructions + ' items';
        }
        let page_numbers = document.querySelectorAll(this.pageNumbers + '.current');
        for (let i = 0; i < page_numbers.length; i++) {
            let page_number = page_numbers[i];
            page_number.innerHTML = page;
        }
    }

    setTableContainerHeight() {//
        let table_element = document.querySelector(this.tableElementId);
        if (!table_element) {
            return;
        }
        let table_height = table_element.offsetHeight;
        let table_element2 = document.querySelector(this.searchSubmitElementId);
        if (!table_element2) {
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

    removeTableRow(del_row){//remove table row by  having link in row <a id=''
        let link_to_delete = document.querySelector("#"+this.tableRowHrefPrefix + del_row);
        if (link_to_delete) {
            link_to_delete.parentElement.parentElement.remove();//remove tr
        }
    }
    
    displayAlert(msg){
        alert(msg);
    }
    
    showRowDetails(e,rowid){
        e.preventDefault();
        console.log(rowid);
    }
}



class Oc3SengineSourceManager extends Oc3SengineRowsManager{
    
    constructor(options) {
        super(options);
        this.postType = options.post_type;//variable oc3wp_gpt_loadnoncec
        this.indexPostAction = options.index_action;
        this.rowEmbednonce = options.row_Embednonce;
        this.indexedManager = options.indexed_Manager;
    }
    
    populateTBody(){
        
        let rows = '';
        for (let idx in this.rowItems) {
            let row_o = this.rowItems[idx];
            let bot_options = row_o.bot_options;//JSON.parse();
            console.log(bot_options);
            //JSON.parse()
            let tr = '<tr class="';
            if (row_o.disabled === '1') {
                tr += 's2baia_disabled_text';
            }
            tr += '">';
            let td1 = '<td class="id_column">' + row_o.id + '</td>';
            let td2 = '<td><a href="'+row_o.post_editurl+'" id="s2baia_bot_href_' + row_o.id + '"  target="blank">' + row_o.post_title + '</a></td>';
            
            let td3 = '<td class="mvertical"><span id="s2baia_model_span_' + row_o.id + '">' + row_o.post_type + '</span></td>';
            
            
            
            let td6 = '<td class="s2baia_flags_td"><span title="edit" class="dashicons  dashicons-controls-play" onclick="oc3se_sources_list.indexRow(event,' + row_o.id + ",'"+this.appSuffix+'\' )"></span></td> ';
            
            tr = tr + td1 + td2 + td3 + td6 + '</tr>';
            rows = rows + tr;
        }
        return rows;
    }
    loadRows(elementSelector, side, distance,successcallback) {//oc3sengineLoadLogs
        const oc3wpdata = this.collectRowData();
        oc3wpdata.postType = this.postType;
        this.putLoader(elementSelector, side, distance);
        this.performRequestCall(this.loadRowCallbacks(successcallback), oc3wpdata);
    }
 
    indexRow(e, post_id){
        e.preventDefault();
        let oc3wpdata = {
            oc3se_embednonce: this.rowEmbednonce,
            action: this.indexPostAction,
            id: post_id
        };
        //document.querySelector(this.rowPageId).value = 1;
        this.putLoader(this.rowsContainerId, 'left', -70);
        oc3wpdata.sendAjax = 1;
        oc3wpdata.sendJson = 1;
        this.performRequestCall(this.indexRowCallbacks(), oc3wpdata);
        
    }
    
    indexRowCallbacks(){
        return {
            ajaxBefore: this.showLoader,
            ajaxSuccess: res => this.ajaxSuccessIndexRow(res),
            ajaxComplete: this.hideLoader
        };
        //this.ajaxSuccessUpdateRow(res)
    }
    
    ajaxSuccessIndexRow(res){
        console.log('ajaxSuccessIndexRow');//console.log('ajaxSuccessUpdateBot');
        console.log(res);
        if(!('result' in res)){
            alert(oc2sengine_error_1);//variable should be initialized in php file
        }
        if(res.result == 200){
            let indexer = this.indexedManager;
            
            if(indexer){
                indexer.loadRowsI();
            }
            
        }else if('msg' in res){
            alert(res.msg);
        }
    }
    
}

class Oc3SengineIndexedManager extends Oc3SengineRowsManager{
    
    constructor(options) {
        super(options);
    }
    
    loadRowsI(){
        document.querySelector(this.rowPageId).value = 1;
        let ocsearchdata = {
            loadnonce: this.rowLoadnonce,
            action: this.loadRowsAction,
            rows_per_page: document.querySelector(this.rowsPerPageId).value,
            search: document.querySelector(this.searchInputId).value,
            page: document.querySelector(this.rowPageId).value,
            sendAjax:1
        };
        let boundajaxSuccessLoadRow = this.ajaxSuccessLoadRow.bind(this);
        this.putLoader(this.rowsContainerId, 'right', -70);
        this.performRequestCall(this.loadRowCallbacks(boundajaxSuccessLoadRow), ocsearchdata);

    }
    loadRows(elementSelector, side, distance,successcallback) {//oc3sengineLoadLogs
        const oc3wpdata = this.collectRowData();
        this.putLoader(elementSelector, side, distance);
        this.performRequestCall(this.loadRowCallbacks(successcallback), oc3wpdata);
    }
    populateTBody(){
        let rows = '';
        for (let idx in this.rowItems) {
            
     
            let row_o = this.rowItems[idx];

            //JSON.parse()
            let tr = '<tr id="oc3se_indexedtblrow'+row_o.id+'">';

            //tr += '">';
            let td1 = '<td class="id_column">' + row_o.id + '</td>';
            let td2 = '<td><a href="#" onclick="oc3se_indxed_list.showRowDetails(event,' + row_o.id + ',\'\')" id="s2baia_bot_href_' + row_o.id + '"  target="blank">' + row_o.title + '</a></td>';
            
            let td3 = '<td >' + row_o.details + '</td>';
            let td4 = '<td >' + row_o.source_type + '</td>';
            let td5 = '<td >'+ row_o.dateupdated + '</td>';
            
            
            let td6 = '<td class="s2baia_flags_td"><span title="remove" class="dashicons  dashicons-trash" onclick="oc3se_indxed_list.removeIdxRow(event,' + row_o.id + ",'"+this.appSuffix+'\' )"></span></td> ';
            
            tr = tr + td1 + td2 + td3 + td4 + td5+ td6 + '</tr>';
            rows = rows + tr;
            
        }
        return rows;
    }
    ajaxSuccessLoadRow(res){
        console.log('ajaxSuccessLoadRow');//console.log('ajaxSuccessUpdateBot');
        console.log(res);
        
        
        if (!('result' in res) || res.result != 200) {
            if('msg' in res){
                this.displayAlert(res.msg);
            }
            return;
        }
        
        if(!('index_rows' in res )){
            return;
        }
        
        
        this.rowItems = res.index_rows;
        let pagidata = {'page': res.page, 'items_per_page': res.rows_per_page, 'total': res.total};

        let tbody = document.querySelector('#oc3sengine-indexed-list'+this.appSuffix);
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        let rows = '';
        for (let idx in this.rowItems) {
            
     
            let row_o = this.rowItems[idx];

            //JSON.parse()
            let tr = '<tr id="oc3se_indexedtblrow'+row_o.id+'">';

            //tr += '">';
            let td1 = '<td class="id_column">' + row_o.id + '</td>';
            let td2 = '<td><a href="#" onclick="oc3se_indxed_list.showRowDetails(event,' + row_o.id + ',\'\')" id="s2baia_bot_href_' + row_o.id + '"  target="blank">' + row_o.title + '</a></td>';
            
            let td3 = '<td >' + row_o.details + '</td>';
            let td4 = '<td >' + row_o.source_type + '</td>';
            let td5 = '<td >'+ row_o.dateupdated + '</td>';
            
            
            let td6 = '<td class="s2baia_flags_td"><span title="remove" class="dashicons  dashicons-trash" onclick="oc3se_indxed_list.removeIdxRow(event,' + row_o.id + ",'"+this.appSuffix+'\' )"></span></td> ';
            
            tr = tr + td1 + td2 + td3 + td4 + td5+ td6 + '</tr>';
            rows = rows + tr;
        
        }
        tbody.innerHTML = rows;

        this.showPagination(pagidata);
        let totals = document.querySelectorAll(this.totalRows);
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }
    }
    

   deleteIdxRow(oc3data) {
        oc3data.sendAjax = 1;
        oc3data.sendJson = 1;
        this.performRequestCall(this.deleteIdxRowCallbacks(), oc3data);
    }
   deleteIdxRowCallbacks() {
        return {
            ajaxBefore: this.showLoader,
            ajaxSuccess: res => this.ajaxSuccessDeleteIdxRow(res),
            ajaxComplete: this.hideLoader
        };
    }
   ajaxSuccessDeleteIdxRow(res){
        console.log('ajaxSuccessDeleteIdxRow');
        console.log(res);
        if (!'result' in res ) {
            return;
        }
        
        if(res.result != 200){
            if('msg' in res){
                this.displayAlert(res.msg );
            }
            return;
        }
        
        let del_row = res.del_row;
        let link_to_delete = document.querySelector("#"+this.tableRowHrefPrefix + del_row);
        if (link_to_delete) {
            link_to_delete.remove();//remove tr
        }

    }
    removeIdxRow(e, row_id) {
        e.preventDefault();
        let wantRemove = confirm(this.messageLogConfirmDelete + ':' + row_id);
        if (!wantRemove) return;

        let oc3wpdata = {
            oc3se_row_dellognonce: this.rowDellogNonce,
            action: this.deleteRowsAction,
            id: row_id
        };
        document.querySelector(this.rowPageId).value = 1;
        this.putLoader(this.rowsContainerId, 'left', -70);
        this.deleteIdxRow(oc3wpdata);
    }
    showRowDetails(e,chunk_id){
        e.preventDefault();
        let oc3sedata = {chunk_id:chunk_id,oc3se_gpt_loadnonce: this.rowLoadnonce,
            action: 'oc3se_show_indexed_content'};
        oc3sedata.sendAjax = 1;
        oc3sedata.sendJson = 1;
        this.performRequestCall(this.showDetailsCallbacks(), oc3sedata);
    }
    
   showDetailsCallbacks() {
        return {
            ajaxBefore: this.showLoader,
            ajaxSuccess: res => this.ajaxSuccessShowDetails(res),
            ajaxComplete: this.hideLoader
        };
    }
   ajaxSuccessShowDetails(res){
        console.log('ajaxSuccessShowDetails');
        console.log(res);
        if (!'result' in res ) {
            return;
        }
        
        if(res.result != 200){
            if('msg' in res){
                this.displayAlert(res.msg );
            }
            return;
        }
        
        let row_content = res.content;

        document.querySelectorAll('.oc3seox_modal_content')[0].innerHTML = row_content;
        document.querySelectorAll('.oc3seox-overlay')[0].style.display = 'block';
        document.querySelectorAll('.oc3seox_modal')[0].style.display = 'block';
        this.s2baiaImageCloseModal();
    }
    s2baiaImageCloseModal() {
        document.querySelectorAll('.oc3seox_modal_close')[0].addEventListener('click', event => {
            document.querySelectorAll('.oc3seox_modal_content')[0].innerHTML = '';
            document.querySelectorAll('.oc3seox-overlay')[0].style.display = 'none';
            document.querySelectorAll('.oc3seox_modal')[0].style.display = 'none';
        });
    }
    
}