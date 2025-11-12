let oc3sengineDefaultAgent = {
    init: function (){
        this.search();
        return this;
    },
    search: function(){
        let thisvar = this;
        let oc3sengine_search_boxes = document.getElementsByClassName('oc3sengine-search');
        if(oc3sengine_search_boxes && oc3sengine_search_boxes.length){
            for(let i=0;i<oc3sengine_search_boxes.length;i++){
                let oc3sengine_search_box = oc3sengine_search_boxes[i];
                let oc3sengine_search_form = oc3sengine_search_box.getElementsByClassName('oc3sengine-search-form')[0];
                let oc3sengine_search_field = oc3sengine_search_box.getElementsByClassName('oc3sengine-search-field')[0];
                let oc3sengine_search_result = oc3sengine_search_box.getElementsByClassName('oc3sengine-search-result')[0];
                let oc3sengine_search_button = oc3sengine_search_box.getElementsByClassName('oc3sengine-search-submit')[0];
                oc3sengine_search_button.addEventListener('click', function (){
                    thisvar.searchInfo(oc3sengine_search_result,oc3sengine_search_field);
                });
                oc3sengine_search_form.addEventListener('submit', function (e){
                    thisvar.searchInfo(oc3sengine_search_result,oc3sengine_search_field);
                    e.preventDefault();
                    return false;
                });
                
                oc3sengine_search_field.addEventListener('keyup', function (e){
                    e.preventDefault();
                    if(!oc3sengine_search_field){
                        return;
                    }
                    let search_text = oc3sengine_search_field.value;
                    if(search_text.length < 1){
                        oc3sengine_search_result.innerHTML = "";
                        oc3sengine_search_result.style.paddingBottom = '0';
                    }
                    
                    return false;
                });
            }
        }
    },
    searchInfo: function (oc3sengine_search_result,oc3sengine_search_field){
        let search = oc3sengine_search_field.value;
        if(search !== '') {
            oc3sengine_search_result.innerHTML = '<div class="oc3sengine-search-loading" ><div class="oc3sengine-custom-loader oc3sengine-general-loader" style="display: grid;"></div></div>';
            oc3sengine_search_result.classList.remove('oc3sengine-has-item');
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', oc3sengineParams.ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send('action=oc3_ajax_search_data&_wpnonce='+oc3sengineParams.search_nonce+'&search='+encodeURIComponent(search));
            xhttp.onreadystatechange = function(oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        oc3sengine_search_result.classList.add('oc3sengine-has-item');
                        var oc3sengine_response = this.responseText;
                        if (oc3sengine_response !== '') {
                            oc3sengine_response = JSON.parse(oc3sengine_response);
                            oc3sengine_search_result.innerHTML = '';
                            if (oc3sengine_response.result === 200) {
                                console.log(oc3sengine_response);
                                if(oc3sengine_response.data.length){
                                    
                                    for(let i = 0; i < oc3sengine_response.data.length; i++){
                                        let item = oc3sengine_response.data[i];
                                        let link = document.createElement("a");
                                        link.setAttribute("href", item.link);
                                        link.setAttribute("target", "_blank");
                                        link.innerHTML = item.title;
                                        let linkdiv = document.createElement("div");
                                        linkdiv.setAttribute("class", "oc3sengine_search_item_title");
                                        linkdiv.appendChild(link);

                                        let excerptdiv = document.createElement("div");
                                        excerptdiv.setAttribute("class", "oc3sengine_search_item_excerpt");
                                        excerptdiv.innerHTML = item.excerpt+'... ' + '<a href="'+item.link + '" target="_blank">'
                                        + oc3sengineParams.read_more_msg + '</a>';
                                        
                                        let itemcontentdiv = document.createElement("div");
                                        itemcontentdiv.setAttribute("class", "oc3sengine_search_item_content");
                                        itemcontentdiv.appendChild(excerptdiv);
                                        
                                        let itemdiv = document.createElement("div");
                                        itemdiv.setAttribute("class", "oc3sengine_search_item");
                                        itemdiv.appendChild(linkdiv);
                                        itemdiv.appendChild(itemcontentdiv);
                                        
                                        oc3sengine_search_result.appendChild(itemdiv);
                                        oc3sengine_search_result.style.paddingBottom = '5px';
                                        
                                        
                                    }
                                }
                                else{
                                    oc3sengine_search_result.innerHTML = '<p>'+oc3sengineParams.no_result_msg+'</p>';
                                }
                            }
                            else{
                                oc3sengine_search_result.innerHTML = '<p class="oc3sengine-search-error">'+oc3sengine_response.msg+'</p>';
                            }
                        }
                        else{
                            oc3sengine_search_result.innerHTML = '<p class="oc3sengine-search-error">'+oc3sengineParams.wrong_msg+'</p>';
                        }
                    }
                    else{
                        oc3sengine_search_result.innerHTML = '<p class="oc3sengine-search-error">'+oc3sengineParams.wrong_msg+'</p>';
                    }
                }
            }
        }
    }
};
window['oc3sengineDefaultAgent'] = oc3sengineDefaultAgent.init();