import{b as r}from"./react-vendor-D7WUTLsu.js";const i=()=>(r.useEffect(()=>{const e="mobile-premium-styles";if(!document.getElementById(e)){const t=document.createElement("style");t.id=e,t.innerHTML=`
        /* 🚫 Hide Scrollbar (Clean App Look) */
        ::-webkit-scrollbar {
          width: 0px;
          background: transparent;
          display: none;
        }
        
        /* Smooth Scrolling for Mobile */
        html {
          scroll-behavior: smooth;
          -webkit-tap-highlight-color: transparent;
        }
      `,document.head.appendChild(t)}},[]),r.useEffect(()=>{const e=t=>{if(typeof navigator!="undefined"&&navigator.vibrate)try{navigator.vibrate(10)}catch(o){}};return window.addEventListener("click",e),()=>window.removeEventListener("click",e)},[]),null);export{i as default};
