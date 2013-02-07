<!--
//addMenuBorder(pMenu, window.subBlank, null, '#666666', 1, '#CCCCDD', 2);
// [opacity, 'layer colour', X offset, Y offset, Width Difference, Height difference]
//addDropShadow(pMenu,window.subM,[40,"#666666",2,2,0,0]);
if (!isOp && navigator.userAgent.indexOf('rv:0.')==-1){
 pMenu.showMenu = new Function('mN','menuAnim(this, mN, 10)');
 pMenu.hideMenu = new Function('mN','menuAnim(this, mN, -10)');
}
if (!isNS4){ pMenu.update(true);}
else{
 var popOldOL = window.onload;
 window.onload = function(){ if (popOldOL) popOldOL(); pMenu.update(); }
}
var nsWinW = window.innerWidth, nsWinH = window.innerHeight, popOldOR = window.onresize;
window.onresize = function(){
 if (popOldOR) popOldOR();
 if (isNS4 && (nsWinW!=innerWidth || nsWinH!=innerHeight)) history.go(0);
 pMenu.position();
}
window.onscroll = function(){ pMenu.position();}
if (isNS4){
 document.captureEvents(Event.CLICK);
 document.onclick = function(evt){ with (pMenu) if (overI) click(overM, overI); return document.routeEvent(evt); }
}
if (!isIE || isOp){
 var nsPX=pageXOffset, nsPY=pageYOffset;
 setInterval('if (nsPX!=pageXOffset || nsPY!=pageYOffset) ' +
 '{ nsPX=pageXOffset; nsPY=pageYOffset; window.onscroll() }', 50);
}
function menuAnim(menuObj, menuName, dir){
 var mD = menuObj.menu[menuName][0];
 if (!mD.timer) mD.timer = 0;
 if (!mD.counter) mD.counter = 0;
 with (mD){
  clearTimeout(timer);
  if (!lyr || !lyr.ref) return;
  if (dir>0) lyr.vis('visible');
  lyr.sty.zIndex = 1001 + dir;
  lyr.clip(0, 0, menuW+2, (menuH+2)*Math.pow(Math.sin(Math.PI*counter/200),0.75) );
  if ((isDOM&&!isIE) && (counter>=100)) lyr.sty.clip='';
  counter += dir;
  if (counter>100) counter = 100;
  else if (counter<0) { counter = 0; lyr.vis('hidden') }
  else timer = setTimeout(menuObj.myName+'.'+(dir>0?'show':'hide')+'Menu("'+menuName+'")', 40);
 }
}
function menuFilterShow(menuObj, menuName, filterName){
 var mD = menuObj.menu[menuName][0];
 with (mD.lyr){
  sty.filter = filterName;
  var f = ref.filters;
  if (f&&f.length&&f[0]) f[0].Apply();
  vis('visible');
  if (f&&f.length&&f[0]) f[0].Play();
 }
}
function addMenuBorder(mObj, iS, alpha, bordCol, bordW, backCol, backW){
 for (var mN in mObj.menu){
  var mR=mObj.menu[mN], dS='<div style="position:absolute; background:';
  if (mR[0].itemSty != iS) continue;
  for (var mI=1; mI<mR.length; mI++) {
   mR[mI].iX += bordW+backW;
   mR[mI].iY += bordW+backW;
  }
  mW = mR[0].menuW += 2*(bordW+backW);
  mH = mR[0].menuH += 2*(bordW+backW);
  if (isNS4) mR[0].extraHTML += '<layer bgcolor="'+bordCol+'" left="0" top="0" width="'+mW+
   '" height="'+mH+'" z-index="980"><layer bgcolor="'+backCol+'" left="'+bordW+'" top="'+
   bordW+'" width="'+(mW-2*bordW)+'" height="'+(mH-2*bordW)+'" z-index="990"></layer></layer>';
  else mR[0].extraHTML += dS+bordCol+'; left:0px; top:0px; width:'+mW+'px; height:'+mH+
   'px; z-index:980; '+(alpha!=null?'filter:alpha(opacity='+alpha+'); -moz-opacity:'+(alpha/100):'')+
   '">'+dS+backCol+'; left:'+bordW+'px; top:'+bordW+'px; width:'+(mW-2*bordW)+'px; height:'+
   (mH-2*bordW)+'px; z-index:990"></div></div>';
 }
}
function addDropShadow(mObj, iS){
 for (var mN in mObj.menu){
  var a=arguments, mD=mObj.menu[mN][0], addW=addH=0;
  if (mD.itemSty != iS) continue;
  for (var shad=2; shad<a.length; shad++){
   var s = a[shad];
   if (isNS4) mD.extraHTML += '<layer bgcolor="'+s[1]+'" left="'+s[2]+'" top="'+s[3]+'" width="'+
    (mD.menuW+s[4])+'" height="'+(mD.menuH+s[5])+'" z-index="'+(arguments.length-shad)+'"></layer>';
   else mD.extraHTML += '<div style="position:absolute; background:'+s[1]+'; left:'+s[2]+
    'px; top:'+s[3]+'px; width:'+(mD.menuW+s[4])+'px; height:'+(mD.menuH+s[5])+'px; z-index:'+
    (a.length-shad)+'; '+(s[0]!=null?'filter:alpha(opacity='+s[0]+'); -moz-opacity:'+(s[0]/100):'')+
    '"></div>';
   addW=Math.max(addW, s[2]+s[4]);
   addH=Math.max(addH, s[3]+s[5]);
  }
  mD.menuW+=addW; mD.menuH+=addH;
 }
}
-->