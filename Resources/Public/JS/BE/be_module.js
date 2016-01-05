

$(document).ready(function() {
    // check BE form
//    if ( $(".pxa-social-feed-be").length > 0 ){
        var tokens = $(".js__pxasocialfeed-be-show-all-tokens").find(".js__pxasocialfeed-be-tokens");
        
        var tableToken = $("div.js__pxasocialfeed-be-token-form").find("form").find("table");
        var rowToken = tableToken.find("tr");
        
        // check if there is some token oject
        if (tokens.length > 0 ){
            tokens = tokens.find("tr.js__pxasocialfeed-be-custom-token");
            // add click function for aech token record at table
            tokens.each (function (){
                $(this).mouseover(function(){ $(this).addClass("active"); });
                $(this).mouseleave(function(){ $(this).removeClass("active"); });
                
                $(this).click(function (){
                    var td = $(this).find("td").toArray();
                    rowToken.find("td input[name*='appID']").val( $(td[1]).text() );
                    rowToken.find("td input[name*='appSecret']").val($(td[2]).text() );
                    rowToken.find("td select[name*='tokenType']").val( $(td[3]).find("input").val() );
                    rowToken.find("td input[name*='tokenUid']").val( $(td[0]).text() );
                    var accesTokenUrl = $(td[4]).find("input").val();
                    rowToken.find("td a[name*='GenerateAccessToken']").attr('href', accesTokenUrl );
                    rowToken.find("td a[name*='GenerateAccessToken']").hide();
                    if (typeof accesTokenUrl !== 'undefined' && accesTokenUrl.length > 0) {
                        rowToken.find("td a[name*='GenerateAccessToken']").show();
                    }
                    rowToken.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[delete]']").show();
                    rowToken.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[delete]']").click(function(){
                        rowToken.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[DeleteToken]']").val(1);
                    });
                });
            });
        }

        $(".js__pxasocialfeed-be-token-form-clear-button").click(function(){
            rowToken.find("td input[name*='appID']").val('');
            rowToken.find("td input[name*='appSecret']").val('');
            rowToken.find("td select[name*='tokenType']").val(1);
            rowToken.find("td input[name*='tokenUid']").val(0);
            rowToken.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[DeleteToken]']").val(0);
        });
//    }
    
    // form for creating config
//    if ( $(".pxa-social-feed-be-config").length > 0){
        var configs = $(".js__pxasocialfeed-be-show-all-configs").find(".js__pxasocialfeed-be-configs");
        
        var tableConfig = $(".js__pxasocialfeed-be-config-form").find("form").find("table");
        var rowConfig = tableConfig.find("tr");
        
        if (configs.length > 0){
            configs = configs.find("tr.js__pxasocialfeed-be-custom-config");
            
            configs.each(function() {
                $(this).mouseover(function(){ $(this).addClass("active"); });
                $(this).mouseleave(function(){ $(this).removeClass("active"); });
                
                $(this).click(function (){
                    var td = $(this).find("td").toArray();
                    
                    rowConfig.find("td input[name*='configName']").val( $(td[1]).text() );
                    rowConfig.find("td input[name*='socialId']").val( $(td[2]).text() );
                    rowConfig.find("td input[name*='feedPid']").val( $(td[3]).text() );
                    rowConfig.find("td input[name*='feedCount']").val( $(td[4]).text() );
                    rowConfig.find("td select[name*='token']").val( $(td[6]).text() );                    
                    rowConfig.find("td input[name*='configUid']").val( $(td[0]).text() );
                    
                    rowConfig.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[delete]']").show();
                    rowConfig.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[delete]']").click(function(){
                        rowConfig.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[DeleteConfig]']").val(1);
                    });
                });
            });
        }
        
        $(".js__pxasocialfeed-be-config-form-clear-button").click(function(){
            rowConfig.find("td input[name*='configName']").val("");
            rowConfig.find("td input[name*='socialId']").val("");
            rowConfig.find("td input[name*='feedPid']").val("");
            rowConfig.find("td input[name*='feedCount']").val("");
            rowConfig.find("td select[name*='token']").val(1);
            rowConfig.find("td input[name*='configUid']").val(0);
            rowConfig.find("td input[name='tx_pxasocialfeed_tools_pxasocialfeedimporter[DeleteConfig]']").val(0);
        });
//    }
     
});

