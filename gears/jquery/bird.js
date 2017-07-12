function BirdStamp(){return (new Date()).getTime();}
function BirdToastOut(toast,duration){
    if(!toast){$(".BirdToast").addClass("BirdToastGone").fadeOut("fast",function(){$(this).remove();});return;}
    if(toast.hasClass("BirdToastGone"))return;
    duration=duration?duration:0;
    var toastout=function(){
        toast.stop(true).addClass("BirdToastGone").animate({"margin-top":(0-toast.innerHeight()*2)+"px","opacity":"hide"},"fast",function(){if(toast.hasClass("BirdToastGone"))toast.remove();});
    }
    if(duration>0){setTimeout(function(){toastout();},duration);}else{toastout();}
}
function BirdToastChange(toast,bind,data,duration){
    data=$.trim(data);
    if(!data.length){BirdToastOut(toast);return;}
    duration=duration?duration:0;
    var binder=$(bind);
    if(toast.hasClass("BirdToastGone"))toast.stop(true).removeClass("BirdToastGone").appendTo("body").css({"margin-top":"0"});
    toast.html(data).css({"left":binder.offset().left+"px","top":binder.offset().top+"px","margin-left":(0-Math.round((toast.outerWidth()-binder.innerWidth())/2))+"px"});
    toast.stop(true).animate({"margin-top":(0-toast.innerHeight()-8)+"px","opacity":"show"},"fast");
    if(duration>0)BirdToastOut(toast,duration);
}
function BirdToast(bind,data,duration,delay){
    delay=delay?delay:0;
    duration=duration?duration:1800;
    var toast=$("<div class=\"BirdToast BirdToastGone\" style=\"display:block;position:absolute;font-size:12px;line-height:15px;color:#fff;background:#000;background:rgba(0,0,0,0.8);border-radius:3px;word-break:break-all;padding:5px 10px;cursor:default;z-index:1000000;text-align:center;\"></div>");
    if(delay>0){setTimeout(function(){BirdToastChange(toast,bind,data,duration);},delay);}else{BirdToastChange(toast,bind,data,duration);}
    return toast;
}
function BirdVar(name,value){
    var variant=window.top["_BIRDVAR"]=(window.top["_BIRDVAR"]||{});
    if((value===null)&&variant&&variant[name]){delete variant[name];return variant;}
    return name===undefined?variant:(value!==undefined?variant[name]=value:variant[name]);
}
function BirdRead(param){
    var values={};
    var i=0;
    $(param).each(function(){
        var ipted=true;
        var ipt=$(this);
        var k=ipt.attr("name");
        var v="";
        if(k.length>0){
            if(ipt.is("input[type='checkbox']")){
                ipted=ipt.prop("checked");
                if(k.indexOf("[]")>0){
                    v=values.hasOwnProperty(k)?values[k]:[];
                    if(ipted)v.push(ipt.val());
                }else{v=ipt.val();}}
                else if(ipt.is("input[type='radio']")){ipted=ipt.prop("checked");v=ipt.val();}
                else if(ipt.is(".FormEditor")){window.KindEditors[ipt.attr("id")].sync();v=ipt.val();}
                else if(ipt.is("input,textarea,select")){v=ipt.val();}
                else if(ipt.is("img")){v=ipt.attr("src");}
                else{v=ipt.html();}
                if(ipted){
                    i++;
                    values[k]=v;
                }
            }
        });
    return values;
}
function BirdWrite(param,values){
    $(param).each(function(i){
        var ipt=$(this);
        var k=ipt.attr("name");
        var v=values[(k.indexOf("[]")>0)?k.substr(0,k.length-2):k];
        if((k.length>0)&&(v!==undefined)){
            if(ipt.is("input[type='checkbox']")){
                if(k.indexOf("[]")>0){ipt.prop("checked",$.inArray(ipt.val(),v)>-1);}else{ipt.prop("checked",v);}}
                else if(ipt.is("input[type='radio']")){ipt.prop("checked",v==ipt.val());}
                else if(ipt.is(".FormEditor")){window.KindEditors[ipt.attr("id")].html(v);}
                else if(ipt.is("input,textarea,select")){ipt.val(v);}
                else if(ipt.is("img")){ipt.attr("src",v);}
                else{ipt.html(v);}
            }
        });
}
function BirdPost(bind,param,target,callback,pretext){
    param=param?param:{};
    var formed=((typeof param==="string")||(param instanceof String));
    target=target?target:$(bind).attr("bird");
    pretext=pretext?pretext:"";
    callback=callback?callback:false;
    var callbacked=(Object.prototype.toString.call(callback)=="[object Function]");
    var toast=BirdToast(bind,pretext,3600);
    $.post("/bird.php?BIRD="+target+"&STAMP="+BirdStamp(),formed?BirdRead(param):param,function(data){
        if(!data.bird){BirdToastOut(toast);return;}
        if(data.status.length>0){BirdToastChange(toast,bind,data.status,1800);}else{BirdToastOut(toast);}
        $.each(data.toasts,function(i,t){BirdToast(t.bind=="$"?bind:t.bind,t.toast,t.duration,t.delay);});
        if(formed){BirdWrite(param,data.params);}
        if(callback){if(callbacked){try{callback(data.html,data.params,bind);}catch(e){}}else{$(callback).html(data.html);}}
        $.each(data.scripts,function(i,s){if(s.delay>0){setTimeout(function(){try{eval(s.script);}catch(e){}},s.delay);}else{try{eval(s.script);}catch(e){}}});
    },"json");
    return toast;
}
function BirdUpload(bind,param,target,callback,pretext){
    $(".BirdUpload").remove();
    var dialog=navigator.userAgent.indexOf("MSIE")>0;
    param=param?param:{};
    var formed=((typeof param==="string")||(param instanceof String));
    target=target?target:$(bind).attr("bird");
    pretext=pretext?pretext:"";
    callback=callback?callback:false;
    var callbacked=(Object.prototype.toString.call(callback)=="[object Function]");
    var stamp=BirdStamp();
    var iframe=$("<iframe class=\"BirdUpload\" name=\"BirdFrame"+stamp+"\" style=\"display:none;\" />");
    var form=$("<form class=\"BirdUpload\" method=\"POST\" enctype=\"multipart/form-data\" action=\"/bird.php?BIRD="+target+"&STAMP="+stamp+"\" target=\"BirdFrame"+stamp+"\" style=\""+(dialog?"position:absolute;position:fixed;left:50%;top:50%;width:260px;padding:10px 0;margin:-30px 0 0 -130px;;border:1px solid #ccc;background:#eee;text-align:center;z-index:999999999;":"display:none;")+"\"></form>");
    if(dialog){
        var hint=$(bind).attr("hint");
        form.append("<div style=\"color:#333;font-weight:bold;line-height:20px;font-size:12px;text-align:left;margin:0;padding:0 0 5px 12px;cursor:default;\">"+(hint?hint:"请上传文件：")+"</div>");
        form.append("<a href=\"javascript:void(0);\" style=\"display:block;overflow:hidden;color:#333;font-weight:bold;line-height:20px;font-size:12px;position:absolute;right:10px;top:8px;font-family:Arial;outline:0;\" onmouseover=\"this.style.color='navy';\" onmouseout=\"this.style.color='#333';\" onclick=\"$('.BirdUpload').remove();\">[X]</a>");
    }
    $.each(formed?BirdRead(param):param,function(k,v){
        if((k.indexOf("[]")>0)||(Object.prototype.toString.call(v)=="[object Array]")){
            var o="";$.each(v,function(i,e){o+="<option selected=\"selected\">"+e+"</option>";});
            form.append("<select multiple=\"multiple\" name=\""+k+"\" style=\"display:none;\">"+o+"</select>");
        }else{
            form.append("<textarea name=\""+k+"\" style=\"display:none;\">"+v+"</textarea>");
        }
    });
    form.append("<input id=\"BirdUploadFile"+stamp+"\" type=\"file\" name=\"BirdUpload\">");
    iframe.appendTo("body");
    form.appendTo("body");
    iframe.load(function(){
        if(!$(this).hasClass("Posted"))return false;
        var data=$($(this).contents().get(0)).find("body").text();
        form.remove();
        iframe.remove();
        try{data=$.parseJSON(data);}catch(e){data={};}
        if(!data.bird){BirdToastOut(toast);return;}
        if(data.status.length>0){BirdToastChange(toast,bind,data.status,1800);}else{BirdToastOut(toast);}
        $.each(data.toasts,function(i,t){BirdToast(t.bind=="$"?bind:t.bind,t.toast,t.duration,t.delay);});
        if(formed){BirdWrite(param,data.params);}
        if(callback){if(callbacked){try{callback(data.html,data.params,bind);}catch(e){}}else{$(callback).html(data.html);}}
        $.each(data.scripts,function(i,s){if(s.delay>0){setTimeout(function(){try{eval(s.script);}catch(e){}},s.delay);}else{try{eval(s.script);}catch(e){}}});
    });
    var toast=BirdToast(bind,"",3600);
    $("#BirdUploadFile"+stamp).change(function(){
        BirdToastChange(toast,bind,pretext,3600);;
        iframe.addClass("Posted");
        $(this).parent().submit();
    });
    if(!dialog){
        $("#BirdUploadFile"+stamp).click();
    }
    return toast;
}
function BirdParam(url,param,trimed){
    if(!trimed){
        var unh=url.split("#");
        var uns=unh[0].split("?");
        url=uns[1]?uns[1]:uns[0];
    }
    var params={};
    var match,pl=/\+/g,search=/([^&=]+)=?([^&]*)/g,
    decode=function(s){return decodeURIComponent(s.replace(pl," "));};
    while(match=search.exec(url)){
        var k=decode(match[1]);
        if(k.indexOf("[]")>0){
            k=k.substr(0,k.length-2);
            var v=params.hasOwnProperty(k)?params[k]:[];
            v.push(decode(match[2]));
            params[k]=v;
        }else{params[k]=decode(match[2]);}
    }
    return param?(params.hasOwnProperty(param)?params[param]:null):params;
}
function BirdUrl(url,param,replace){
    if(!param)return url;
    param=((typeof param==="string")||(param instanceof String))?BirdRead(param):param;
    replace=replace?true:false;
    var unh=url.split("#");
    var uns=unh[0].split("?");
    var pms="";
    var pkv=function(k,v){
        if(Object.prototype.toString.call(v)=="[object Array]"){if(k.indexOf("[]")>0)k=k.substr(0,k.length-2);$.each(v,function(i,e){pms+="&"+encodeURIComponent(k)+"[]="+encodeURIComponent(e);});}else{pms+="&"+encodeURIComponent(k)+"="+encodeURIComponent(v);}
    }
    if((!replace)&&uns[1]){$.each(BirdParam(uns[1],false,true),function(k,v){if(!param.hasOwnProperty(k))pkv(k,v);});}
    $.each(param,pkv);
    return uns[0]+(pms.length>0?"?"+pms.substr(1):"")+(unh[1]?"#"+unh[1]:"");
}
function BirdFrameData(name,value){
    var variant=$.dialog.opener["_BirdFrameData"]=($.dialog.opener["_BirdFrameData"]||{});
    if((value===null)&&variant&&variant[name]){delete variant[name];return variant;}
    return name===undefined?variant:(value!==undefined?variant[name]=value:variant[name]);
}
function BirdFrame(bind,url,param,data,callback,caption,width,height){
    if(!$.dialog){$("head").append('<link rel="stylesheet" type="text/css" href="/gears/jquery/dialog/skins/default.css" />');$.getScript("/gears/jquery/dialog/dialog.js", function(){$.getScript("/gears/jquery/dialog/iframe.js",function(){BirdFrame(bind,url,param,data,callback,caption,width,height);});});return false;}
    url=BirdUrl(url,formed?BirdRead(param):param);
    param=param?param:{};
    var formed=((typeof param==="string")||(param instanceof String));
    window["_BirdFrameData"]=data?data:{};
    callback=callback?callback:false;
    caption=caption?caption:$(bind).attr("caption");
    width=width?width:"90%";
    height=height?height:"90%";
    $.dialog.open(url,{"title":caption,"width":width,"height":height,"lock":true,"close":function(){if(Object.prototype.toString.call(callback)=="[object Function]"){try{callback(bind,param,window["_BirdFrameData"]);}catch(e){}}window["_BirdFrameData"]=null;}});
}
function BirdForm(bind,form,param,data,callback,caption,width,height){
    BirdFrame(bind,"/bird.php?BIRD=~"+form+"&STAMP="+BirdStamp(),param,data,callback,caption,width,height);
}