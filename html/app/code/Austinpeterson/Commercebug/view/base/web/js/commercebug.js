jQueryCb(function(){
    var $ = jQueryCb;
    
    var changeToTab = function(tab_id){
        var parts  = tab_id.split('-');
        if(parts[0] != 'tab') { return; }
        parts.shift(); 
        var tab_content_id = parts.join('-');
        $('#commercebug-tab-content').html(                
            $('#'+tab_content_id).html()
        );            
        $('#commercebug-tab-content .pulsestorm-data-table').DataTable();
        setupEventsForTab(tab_id);
    };
    
    var saveLastTabClicked = function(tab_id){
        localStorage.pulsestorm_commercebug_lasttab = tab_id;
    };
    
    var getSelectedTabId    = function(){
        var id = jQueryCb('#commercebug-tabs .active').parent().attr('id').split('_').pop();
        return id;
    };
    
    var getAllTabIds = function(all_tabs){
        var all_ids = jQueryCb.map(all_tabs, function(item){
           return item.id;
        });
        return all_ids;    
    };
    
    var tabForward  = function(all_tabs){
        var all_ids      = getAllTabIds(all_tabs);
        var selected_tab = getSelectedTabId();
        var currentIndex = all_ids.indexOf(selected_tab);   
        if(currentIndex + 1 === all_ids.length)
        {
            currentIndex = -1;
        }
        var nextTab = all_ids[(currentIndex + 1)];
        switchToTab(nextTab);
    };

    var tabBackwards  = function(all_tabs){
        var all_ids      = getAllTabIds(all_tabs);
        var selected_tab = getSelectedTabId();
        var currentIndex = all_ids.indexOf(selected_tab);        
        if(currentIndex === 0)
        {
            currentIndex = all_ids.length;
        }
        var nextTab = all_ids[(currentIndex - 1)];
        switchToTab(nextTab);
    };
        
    var switchToTab = function(tab_id)
    {
        $('#tabs_tabs_tab_'+tab_id+' div').trigger('click');
        setupEventsForTab(tab_id);
    };
        
    var setupEventsForTab = function(tab_id)
    {
        console.log(tab_id);
        if(tab_id === 'tab-commercebug-layout')
        {
            //setup event handlers
            jQueryCb('.pulsestorm_textbox_but').click(function(event){
                event.preventDefault();
                var id = jQueryCb(this).attr('id');
                jQueryCb('#pulsestorm_textbox_container').css('display','block');
                var map = {
                    'pulsestorm_button_view_handle_layout':function(){
                        var label = 'Commerce Bug found ' + 
                            pulsestorm_commerbug_json['page_layout_xml'].length +
                            ' package/handle layout trees. A package/handle' + 
                            " tree contains **all** loaded and merged XML files. \n\n";
                        return label + pulsestorm_commerbug_json['page_layout_xml'];
                    },
                    'pulsestorm_button_view_request_layout':function(){
                        var label = 'Commerce Bug found ' + pulsestorm_commerbug_json['request_layout_xml'].length + 
                        ' page/request layout ' + 
                        'trees. Magento creates a page/request tree by looking' + 
                        'at the handles, and selecting nodes from the ' + 
                        'package/handle tree that match.' + "\n\n";
                        return label + pulsestorm_commerbug_json['request_layout_xml'];
                    },
                    'pulsestorm_button_view_structure_schedule':function(){
                        var label = '';
                        return label + pulsestorm_commerbug_json['scheduled_structure'].join("\n");
                    },
                    'pulsestorm_button_view_xmlfile':function(){
                        var label = 'Commerce Bug found ' + 
                        pulsestorm_commerbug_json['page_layout_xmlfile'].length + 
                        ' sets of layout' + 
                        ' handle XML files.  There are the files Magento loads to ' + 
                        " create the package/handle XML trees.";
                        var files = pulsestorm_commerbug_json['page_layout_xmlfile'];
                        files = jQuery.map(files, function(val){
                            return val.join("\n");
                        });
                        var sep = "\n+--------------------------------------------------+\n";
                        return label + sep + files.join(sep);
                    }
                }; 
                jQueryCb('#pulsestorm_textbox_container_textbox').val(map[id]());                
            });                    
        }    
    };

    var setupSkeleton = function(all_tabs)
    {
        //setup skeleton that will hold actual commercebug
        var string = '<div id="commercebug-container"><div id="commercebug-tabs"></div><div id="commercebug-tab-content" style="min-height: 200px; border: 1px solid #ddd; border-top: 0px;"></div></div>';
        jQueryCb('body').append(string);
        
        //setup HTML/DOM nodes that hold our actual content
        //(there were created via PHP rendering previously)
        jQueryCb('body').append('<div id="div_commercebug" style="display:none"></div>');
        
        jQueryCb('#div_commercebug').append('<h1>Temporary Commerce Bug HTML Source</h1>');
        var all_ids = jQueryCb.map(all_tabs, function(item){
           var id = item.id.split('-').slice(1).join('-');
           jQueryCb('#div_commercebug').append('<div id="'+id+'"><div></div></div>');
        });

    }    
                
    var setupKeyboardShortcuts = function(all_tabs){
        jQueryCb(document).bind('keyup',function(e){	
            var code = (e.keyCode ? e.keyCode : e.which);
            
            //bail if we're in certain tags.  Not ideal as it kills
            //tab navigation, but that's why we let them turn it off
            if( jQueryCb(e.target).is('input') || 
            jQueryCb(e.target).is('textarea') 	||
            jQueryCb(e.target).is('select') 	||
            jQueryCb(e.target).is('option')	)
            {
                return true;
            }
            
            if(code == 76)
            {
                tabForward(all_tabs);
            }
            else if (code == 72)
            {
                tabBackwards(all_tabs);
            }  
        });  
    };
    var setupJqueryHook = function(){
        $('#commercebug-tabs').w2tabs({
            name: 'tabs',
            active: 'tab1',
            tabs: all_tabs,
            onClick: function (event) {
                var tab_id = event.target;
                changeToTab(tab_id);
                saveLastTabClicked(tab_id);            
            }
        });    
    }
    
    var templateTableId = function(div_id)
    {
        return (div_id + '-table').replace('#','');
    }
    
    var templateDataTable = function(id,headers)
    {
        var table_id = templateTableId(id);
        var string = '<table border="1" id="'+table_id+'"  class="pulsestorm-data-table">';
        string += '<thead><tr>';
        jQueryCb.each(headers, function(key, header)
        {
            string += '<th>' + header + '</th>';
        });
        // string += '<tr><td>Class</td></tr>';
        string += '</tr></thead><tbody></tbody></table>';
        
        return string;
    };
    
    var templateDataTableRowWithIndex = function(index, contents)
    {
        var string = '<tr><td valign="top">' + index + '</td><td valign="top">' +
            contents + '</td></tr>';
        return string;            
    
    }
    
    var templateDataTableRow = function(contents)
    {
        var string = '<tr><td valign="top">' +
            contents + '</td></tr>';
        return string;            
    }
    
    var templatePhpClassAndFile = function(className, file)
    {
        var string  = '<pre class="pulsestorm_commercebug_phpclass">' + 
            className + '</pre>' + "\n";
        string      += '<pre class="pulsestorm_commercebug_file">' + 
            file + '</pre>';
        return string;
    }
    
    var setupRequestTab = function(id)        
    {
        var dataTable = templateDataTable(id,['Type','File/Class']);          
        var rows      = '';
        jQueryCb.each(pulsestorm_commerbug_json['controllers'], function(className, item){
            rows += '<tr>';
            rows += '<td valign="top">Interceptor</td>';
            rows += '<td>';
            rows += templatePhpClassAndFile(
                item['interceptor']['className'],item['interceptor']['file']);
            rows += '</td>';
            rows += '</tr>';            
            
            rows += '<tr>';
            rows += '<td valign="top">Controller</td>';
            rows += '<td>';
            rows += templatePhpClassAndFile(
                item['class']['className'],item['class']['file']);
            rows += '</td>';
            rows += '</tr>';                        
        });   
        if(!rows)
        {
            rows = '<tr><td>No controllers found. Probably a full page cache hit.  See System -&gt; Cache Managment</td><td></td></tr>'
        }           
        // var rows      = '<tr><td valign="top">This is a test</td></tr>';
        dataTable     = replaceDataTableStringBodyWithRows(dataTable, rows);
        dataTable     = dataTable.replace('<tbody></tbody>',
            '<tbody>'+rows+'</tbody>');
    
        var selector = id + ' div';
        jQueryCb(selector).append(dataTable); 
    };
    
    var setupSingleRowDataTableTabWithData = function(id, data, labelHead)
    {
        var selector  = id + ' div';
        var dataTable = templateDataTable(id,[labelHead]);  
        
        var rows      = '';
        jQueryCb.each(data, function(className, info){
            rows += templateDataTableRow(templatePhpClassAndFile(
                className, info.file
            ));
        });              
        // var rows      = '<tr><td valign="top">This is a test</td></tr>';
        dataTable     = dataTable.replace('<tbody></tbody>',
            '<tbody>'+rows+'</tbody>');
        jQueryCb(selector).append(dataTable);     
    }
    
    var setupCrudModelTab = function(id)
    {
        setupSingleRowDataTableTabWithData(
            id, pulsestorm_commerbug_json['models'], 'CRUD/AbstractModel'); 
    };

    var setupCollectionsTab = function(id)
    {
        setupSingleRowDataTableTabWithData(
            id, pulsestorm_commerbug_json['collections'], 'Collections');    
    };

    var replaceDataTableStringBodyWithRows = function(dataTable, rows)
    {
        dataTable     = dataTable.replace('<tbody></tbody>',
            '<tbody>'+rows+'</tbody>');        
        return dataTable;    
    };
    
    var templateBlockRowFromItem = function(item)
    {
        var string = '<tr><td><table width="100%"><thead></thead><tbody><tr><td style="width:400px"><strong>Name: </strong><$name$></td><td><pre class="pulsestorm_commercebug_phpclass"><$className$></pre></td></tr><tr><td colspan="2"><pre class="pulsestorm_commercebug_phpfile"><$template$></pre></td></tr></tbody></table></td></tr>';
        if(!item.template)
        {
            item.template = '[no template]';
        }
        return string.replace('<$name$>',item.name) .
            replace('<$className$>',item.className) .
            replace('<$template$>',item.template);            
        return '<tr><td>wwtf mate</td></tr>';
    };
    
    var setupBlocksTab = function(id)
    {
        var dataTable = templateDataTable(id,['Block names, classes, and files']);    
        var rows      = '';
        jQueryCb.each(pulsestorm_commerbug_json['blocks'], function(key, value){
            rows += templateBlockRowFromItem(value);       
        });
        
        dataTable     = replaceDataTableStringBodyWithRows(dataTable, rows);
        var selector = id + ' div';
        jQueryCb(selector).append(dataTable);    
    };

    var templateGraphForm = function(data)
    {
        var string = '<form target="_blank" method="post" action="https://graph.pulsestorm.net/dot">' +
        '<div>' + 
            '<input name="token_as_commercebug" type="hidden" value="<$token$>">' + 
            '<button type="submit">Render Graph</button>' + 
            '<button onclick="jQueryCb(\'#pulsestorm_commercebug_graph_source_container\').toggle();return false;">Show Graph Source</button>' +         
            '<button class="pulsestorm_textbox_but" id="pulsestorm_button_view_handle_layout">View Package/Handle Layout</button>'  + 
            '<button class="pulsestorm_textbox_but" id="pulsestorm_button_view_xmlfile">View Loaded XML Files</button>'             +                                     
            '<button class="pulsestorm_textbox_but" id="pulsestorm_button_view_request_layout">View Page/Request Layout</button>'   + 
            '<button class="pulsestorm_textbox_but" id="pulsestorm_button_view_structure_schedule">View Structure Schedule</button>'+             
        '</div>' + 
        '<div style="display:none;" id="pulsestorm_commercebug_graph_source_container">' +
            '<textarea rows="10" cols="72" name="as_commercebug_dot_console" id="as_commercebug_dot_console"><$graph$></textarea>' +        
        '</div>' +
        '<div style="display:none;" id="pulsestorm_textbox_container">' +
            '<textarea rows="10" cols="72" name="pulsestorm_textbox_container_textbox" id="pulsestorm_textbox_container_textbox"></textarea>' +        
        '</div>';
        
        return string.replace('<$token$>',data.nonce) .
            replace('<$graph$>',data.graph);
    }
    
    var setupLayoutTab = function(id)
    {
        var form = templateGraphForm(pulsestorm_commerbug_json['layouts']);
        
        var dataTable = templateDataTable(id,['Index','Handles']);          
        var rows      = '';
        jQueryCb.each(pulsestorm_commerbug_json['handles'], function(index, handle){
            rows += templateDataTableRowWithIndex(index, handle);
        });              
        // var rows      = '<tr><td valign="top">This is a test</td></tr>';
        dataTable     = replaceDataTableStringBodyWithRows(dataTable, rows);
        dataTable     = dataTable.replace('<tbody></tbody>',
            '<tbody>'+rows+'</tbody>');
                
        var selector = id + ' div';
        jQueryCb(selector).append(form + dataTable);          
    };

    var wrap = function(subject, tag)
    {
        return ['<',tag,'>',subject,'</',tag,'>'].join('');
    };
    
    var wrapPre = function (subject)
    {
        return wrap(subject, 'pre');
    };
    
    var setupSingleRowDataTableTabWithDataWrapPre = function(id, data, labelHead)
    {
        var dataTable = templateDataTable(id,[labelHead]);          
        var rows      = '';
        jQueryCb.each(data, function(className, file){
            rows += templateDataTableRow(wrapPre(file));
        });              
        // var rows      = '<tr><td valign="top">This is a test</td></tr>';
        dataTable     = replaceDataTableStringBodyWithRows(dataTable, rows);
        dataTable     = dataTable.replace('<tbody></tbody>',
            '<tbody>'+rows+'</tbody>');
    
        var selector = id + ' div';
        jQueryCb(selector).append(dataTable);     
    };
    
    var setupOtherFilesTab = function(id)
    {
        return setupSingleRowDataTableTabWithDataWrapPre(
            id, pulsestorm_commerbug_json['other-files'], 'Other Files');   
    };

    var setupEventsTab = function(id)
    {
        return setupSingleRowDataTableTabWithDataWrapPre(
            id, pulsestorm_commerbug_json['dispatched_events'], 'Events');   
    };

    var setupObserversTab = function(id)
    {
        var dataTable = templateDataTable(id,['Name','Class']);          
        var rows      = '';
        jQueryCb.each(pulsestorm_commerbug_json['invoked_observers'], function(className, item){
            tmp     =  '<tr><td><pre><$name$></pre></td>';
            tmp     += '<td><pre class="pulsestorm_commercebug_phpclass"><$className$></pre></td>';
            tmp     += '</tr>';            
            rows    += tmp.replace('<$name$>',item.name).
                replace('<$className$>',item.instance);            
        });              
        // var rows      = '<tr><td valign="top">This is a test</td></tr>';
        dataTable     = replaceDataTableStringBodyWithRows(dataTable, rows);
        dataTable     = dataTable.replace('<tbody></tbody>',
            '<tbody>'+rows+'</tbody>');
    
        var selector = id + ' div';
        jQueryCb(selector).append(dataTable); 
    
    };

    var setupClassLookupTab = function(id)
    {
        var selector = id + ' div';
        var string = '<h2>Class Lookup</h2><form action="/commercebug/lookup" method="post" target="_blank"><input type="text" name="lookup" value="" /><button>Submit</button></form>';        
        jQueryCb(selector).append(string);    
    };

        
    all_tabs = [
            { id: 'tab-commercebug-request', caption: 'Request' },
            { id: 'tab-commercebug-crud-models', caption: 'Crud Models', closable: false },
            { id: 'tab-commercebug-collections', caption: 'Collections', closable: false },
            { id: 'tab-commercebug-blocks', caption: 'Blocks', closable: false },
            { id: 'tab-commercebug-layout', caption: 'Layout', closable: false },
            { id: 'tab-commercebug-other-files', caption: 'Other Files', closable: false },
            { id: 'tab-commercebug-events', caption: 'Events', closable: false },
            { id: 'tab-commercebug-observers', caption: 'Observers', closable: false },
            { id: 'tab-commercebug-class-lookup', caption: 'Class Lookup', closable: false },            
            // { id: 'tab-commercebug-tasks', caption: 'Tasks', closable: false }                        
    ];

    setupSkeleton(all_tabs);    
    setupJqueryHook();
    
    setupRequestTab('#commercebug-request');
    setupCrudModelTab('#commercebug-crud-models');
    setupCollectionsTab('#commercebug-collections');
    setupBlocksTab('#commercebug-blocks');
    setupLayoutTab('#commercebug-layout');
    setupOtherFilesTab('#commercebug-other-files');
    setupEventsTab('#commercebug-events');
    setupObserversTab('#commercebug-observers');
    setupClassLookupTab('#commercebug-class-lookup');
    
    var tab_id = localStorage.pulsestorm_commercebug_lasttab;
    if(!tab_id)
    {
        tab_id = 'tab-commercebug-request';
    }
    switchToTab(tab_id);    
    setupKeyboardShortcuts(all_tabs);
});