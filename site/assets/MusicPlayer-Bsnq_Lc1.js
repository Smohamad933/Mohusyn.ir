var Ne=Object.defineProperty,Me=Object.defineProperties;var Se=Object.getOwnPropertyDescriptors;var ie=Object.getOwnPropertySymbols;var Te=Object.prototype.hasOwnProperty,ze=Object.prototype.propertyIsEnumerable;var ne=(o,c,p)=>c in o?Ne(o,c,{enumerable:!0,configurable:!0,writable:!0,value:p}):o[c]=p,b=(o,c)=>{for(var p in c||(c={}))Te.call(c,p)&&ne(o,p,c[p]);if(ie)for(var p of ie(c))ze.call(c,p)&&ne(o,p,c[p]);return o},j=(o,c)=>Me(o,Se(c));import{c as v,u as Ce,j as t,X as Re}from"./index-DuEQaH5e.js";import{b as l}from"./react-vendor-D7WUTLsu.js";import{R as Ie,P as $e}from"./rotate-ccw-Dwjyyb5A.js";import{P as Ee}from"./play-taFFWXlF.js";import{H as De}from"./heart-CNutY2uf.js";import{E as Pe}from"./external-link-BSqcUjgZ.js";const Ye=v("ListMusic",[["path",{d:"M21 15V6",key:"h1cx4g"}],["path",{d:"M18.5 18a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z",key:"8saifv"}],["path",{d:"M12 12H3",key:"18klou"}],["path",{d:"M16 6H3",key:"1wxfjs"}],["path",{d:"M12 18H3",key:"11ftsu"}]]),Ve=v("Repeat1",[["path",{d:"m17 2 4 4-4 4",key:"nntrym"}],["path",{d:"M3 11v-1a4 4 0 0 1 4-4h14",key:"84bu3i"}],["path",{d:"m7 22-4-4 4-4",key:"1wqhfi"}],["path",{d:"M21 13v1a4 4 0 0 1-4 4H3",key:"1rx37r"}],["path",{d:"M11 10h1v4",key:"70cz1p"}]]),Be=v("Repeat",[["path",{d:"m17 2 4 4-4 4",key:"nntrym"}],["path",{d:"M3 11v-1a4 4 0 0 1 4-4h14",key:"84bu3i"}],["path",{d:"m7 22-4-4 4-4",key:"1wqhfi"}],["path",{d:"M21 13v1a4 4 0 0 1-4 4H3",key:"1rx37r"}]]),Fe=v("RotateCw",[["path",{d:"M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8",key:"1p45f6"}],["path",{d:"M21 3v5h-5",key:"1q7to0"}]]),He=v("Shuffle",[["path",{d:"M2 18h1.4c1.3 0 2.5-.6 3.3-1.7l6.1-8.6c.7-1.1 2-1.7 3.3-1.7H22",key:"1wmou1"}],["path",{d:"m18 2 4 4-4 4",key:"pucp1d"}],["path",{d:"M2 6h1.9c1.5 0 2.9.9 3.6 2.2",key:"10bdb2"}],["path",{d:"M22 18h-5.9c-1.3 0-2.6-.7-3.3-1.8l-.5-.8",key:"vgxac0"}],["path",{d:"m18 14 4 4-4 4",key:"10pe0f"}]]),Ae=v("SkipBack",[["polygon",{points:"19 20 9 12 19 4 19 20",key:"o2sva"}],["line",{x1:"5",x2:"5",y1:"19",y2:"5",key:"1ocqjk"}]]),Le=v("SkipForward",[["polygon",{points:"5 4 15 12 5 20 5 4",key:"16p6eg"}],["line",{x1:"19",x2:"19",y1:"5",y2:"19",key:"futhcm"}]]),Ge=v("Volume2",[["polygon",{points:"11 5 6 9 2 9 2 15 6 15 11 19 11 5",key:"16drj5"}],["path",{d:"M15.54 8.46a5 5 0 0 1 0 7.07",key:"ltjumu"}],["path",{d:"M19.07 4.93a10 10 0 0 1 0 14.14",key:"1kegas"}]]),Xe=v("VolumeX",[["polygon",{points:"11 5 6 9 2 9 2 15 6 15 11 19 11 5",key:"16drj5"}],["line",{x1:"22",x2:"16",y1:"9",y2:"15",key:"1ewh16"}],["line",{x1:"16",x2:"22",y1:"9",y2:"15",key:"5ykzw1"}]]);function qe(o,c){const p=`mh_state_${o}`,[N,$]=l.useState(()=>{try{const f=localStorage.getItem(p);return f?b(b({},c),JSON.parse(f)):c}catch(f){return c}});l.useEffect(()=>{try{localStorage.setItem(p,JSON.stringify(N))}catch(f){}},[N,p]);const E=l.useCallback(f=>$(C=>typeof f=="function"?f(C):f),[]);return[N,E,!0]}const er=({isPlaying:o,togglePlay:c})=>{var se;const[p,N]=l.useState(!1),[$,E]=l.useState(!1),[f,C]=l.useState("00:00"),[le,W]=l.useState("00:00"),[ce,V]=l.useState("0%"),[B,de]=l.useState(!1),[J,Z]=l.useState(!1),[m,K]=l.useState(!1),[F,H]=l.useState([]),[D,M]=l.useState(!1),U=l.useRef(!1),P=Ce().music,R=P.folders&&P.folders.length?P.folders:[{key:"songs",label:"Songs",emoji:"🎵"}],[Y,S,Q]=qe("music_player",{currentTrackIndex:0,volume:70,isMuted:!1,shuffleMode:!1,repeatMode:"off",activeFolder:R[0].key,savedPosition:0}),{currentTrackIndex:x,volume:T,isMuted:y,shuffleMode:A,repeatMode:z,activeFolder:ee}=Y,L=e=>{S(r=>j(b({},r),{currentTrackIndex:typeof e=="function"?e(r.currentTrackIndex):e,savedPosition:0}))},pe=e=>S(r=>j(b({},r),{volume:e})),re=e=>S(r=>j(b({},r),{isMuted:typeof e=="function"?e(r.isMuted):e})),ue=e=>S(r=>j(b({},r),{shuffleMode:typeof e=="function"?e(r.shuffleMode):e})),me=e=>S(r=>j(b({},r),{repeatMode:typeof e=="function"?e(r.repeatMode):e})),te=e=>S(r=>j(b({},r),{activeFolder:e})),i=l.useRef(null),G=l.useRef(null),a=l.useRef(null),I=l.useRef(null),X=l.useRef(null),g=(P.tracks||[]).map(e=>{const r=e.youtubeId||(e.url&&/youtu/.test(e.url)?(e.url.match(/(?:v=|be\/|embed\/)([A-Za-z0-9_-]{6,})/)||[])[1]:"");return{title:e.title||"Untitled",artist:e.artist||"",category:e.category||R[0].key,type:r?"youtube":"local",youtubeId:r||void 0,src:r?void 0:e.src||e.url,cover:e.cover||(r?`https://img.youtube.com/vi/${r}/maxresdefault.jpg`:"/img/cover1.jpg")}}),xe=g.length?Math.min(x,g.length-1):0,u=g[xe]||{title:"No track",artist:"",cover:"/img/cover1.jpg",type:"local",src:"",category:R[0].key},ge=g.length>0,d=(u==null?void 0:u.type)==="youtube",oe=!!(u&&((se=R.find(e=>e.key===u.category))!=null&&se.spooky)),fe=g.map((e,r)=>({track:e,globalIdx:r})).filter(({track:e})=>e.category===ee);l.useEffect(()=>{if(!(!Q||U.current)&&Y.savedPosition>5&&m&&a.current){try{a.current.seekTo(Y.savedPosition,!0),console.log(`[MusicPlayer] Resumed from ${Math.floor(Y.savedPosition)}s`)}catch(e){}U.current=!0}},[Q,m]),l.useEffect(()=>{var n;if(window.YT&&window.YT.Player){Z(!0);return}if(document.querySelector('script[src="https://www.youtube.com/iframe_api"]'))return;const r=document.createElement("script");r.src="https://www.youtube.com/iframe_api",r.async=!0;const s=document.getElementsByTagName("script")[0];(n=s.parentNode)==null||n.insertBefore(r,s),window.onYouTubeIframeAPIReady=()=>{Z(!0)}},[]),l.useEffect(()=>{if(!J||!d||!u.youtubeId)return;const e=setTimeout(()=>{try{a.current&&(a.current.destroy(),a.current=null),K(!1),V("0%"),C("00:00"),a.current=new window.YT.Player("youtube-player",{height:"0",width:"0",videoId:u.youtubeId,playerVars:{autoplay:0,controls:0,disablekb:1,fs:0,modestbranding:1,playsinline:1},events:{onReady:r=>{K(!0),r.target.setVolume(y?0:T),(o||D)&&setTimeout(()=>{r.target.playVideo(),D&&!o&&c(),M(!1)},100)},onStateChange:r=>{r.data===0&&ae()},onError:r=>{console.error("YouTube Error:",r.data),q(!0)}}})}catch(r){console.error("Error creating YouTube player:",r)}},100);return()=>clearTimeout(e)},[x,J]),l.useEffect(()=>{(async()=>{if(d){if(a.current&&m)try{o?(a.current.playVideo(),N(!0)):a.current.pauseVideo()}catch(r){console.error("YouTube control error:",r)}}else if(i.current)try{o?(await i.current.play(),N(!0)):i.current.pause()}catch(r){console.error("Audio play error:",r)}})()},[o,m,d]),l.useEffect(()=>{I.current&&clearInterval(I.current);const e=()=>{if(d&&a.current&&m)try{const s=a.current.getCurrentTime(),n=a.current.getDuration();if(n&&n>0){const k=100/n*s;V(`${k}%`);const h=Math.floor(s/60),w=Math.floor(s-h*60),_=Math.floor(n/60),O=Math.floor(n-_*60);C(`${h<10?"0"+h:h}:${w<10?"0"+w:w}`),W(`${_<10?"0"+_:_}:${O<10?"0"+O:O}`)}}catch(s){}else if(!d&&i.current){const s=i.current;if(s.duration&&s.duration>0){const n=100/s.duration*s.currentTime;V(`${n}%`);const k=Math.floor(s.currentTime/60),h=Math.floor(s.currentTime-k*60),w=Math.floor(s.duration/60),_=Math.floor(s.duration-w*60);C(`${k<10?"0"+k:k}:${h<10?"0"+h:h}`),W(`${w<10?"0"+w:w}:${_<10?"0"+_:_}`)}}};o&&(e(),I.current=setInterval(e,100));let r=null;return o&&d&&m&&(r=setInterval(()=>{try{if(a.current){const s=a.current.getCurrentTime();s>0&&S(n=>j(b({},n),{savedPosition:s}))}}catch(s){}},5e3)),()=>{I.current&&clearInterval(I.current),r&&clearInterval(r)}},[o,d,m,x]),l.useEffect(()=>{if(d||!i.current)return;const e=()=>ae();return i.current.addEventListener("ended",e),D&&i.current&&i.current.play().then(()=>{o||c(),M(!1)}).catch(r=>{console.error("Auto-play error:",r),M(!1)}),()=>{var r;(r=i.current)==null||r.removeEventListener("ended",e)}},[x,d,z,D]),l.useEffect(()=>{d&&a.current&&m?a.current.setVolume(y?0:T):i.current&&(i.current.volume=y?0:T/100)},[T,y,d,m]);const he=()=>{const e=g.map((r,s)=>s).filter(r=>r!==x);return e[Math.floor(Math.random()*e.length)]},ae=()=>{z==="one"?d&&a.current?(a.current.seekTo(0),a.current.playVideo()):i.current&&(i.current.currentTime=0,i.current.play()):z==="all"||x<g.length-1?q(!0):o||c()},q=(e=!1)=>{let r;A?r=he():r=(x+1)%g.length,H(s=>[...s,x]),o&&M(!0),L(r),!o&&e&&M(!0)},be=()=>{let e;F.length>0?(e=F[F.length-1],H(r=>r.slice(0,-1))):e=(x-1+g.length)%g.length,o&&M(!0),L(e)},ye=()=>{if(d&&a.current&&m)try{const e=a.current.getCurrentTime();a.current.seekTo(Math.max(0,e-10))}catch(e){console.error("Skip back error:",e)}else i.current&&(i.current.currentTime=Math.max(0,i.current.currentTime-10))},ke=()=>{if(d&&a.current&&m)try{const e=a.current.getCurrentTime(),r=a.current.getDuration();a.current.seekTo(Math.min(r,e+10))}catch(e){console.error("Skip forward error:",e)}else i.current&&(i.current.currentTime=Math.min(i.current.duration,i.current.currentTime+10))},ve=e=>{H(r=>[...r,x]),M(!0),L(e),E(!1),te(g[e].category)},we=e=>{if(G.current){const r=G.current;let n=100*(e.pageX-r.getBoundingClientRect().left)/r.offsetWidth;if(n>100&&(n=100),n<0&&(n=0),d&&a.current&&m)try{const h=a.current.getDuration()*n/100;a.current.seekTo(h,!0)}catch(k){console.error("Seek error:",k)}else i.current&&(i.current.currentTime=i.current.duration*n/100);o||c()}},_e=e=>{if(X.current){const r=X.current;let n=100*(e.pageX-r.getBoundingClientRect().left)/r.offsetWidth;n>100&&(n=100),n<0&&(n=0),pe(Math.round(n)),y&&n>0&&re(!1)}},je=()=>{me(e=>e==="off"?"all":e==="all"?"one":"off")};return ge?t.jsxs(t.Fragment,{children:[!d&&t.jsx("audio",{ref:i,src:u==null?void 0:u.src}),t.jsx("div",{id:"youtube-player",style:{display:"none",position:"absolute",top:-9999}}),p&&t.jsxs("div",{className:"fixed bottom-24 left-1/2 transform -translate-x-1/2 md:left-10 md:transform-none z-[100] animate-in slide-in-from-bottom-10 fade-in duration-500 w-full flex justify-center md:block pointer-events-none md:pointer-events-auto",children:[t.jsx("style",{children:`
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
            
            /* Base player card - Mobile first (compact) */
            .player-card {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                width: 340px; 
                min-height: 400px;
                box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.2);
                border-radius: 20px;
                padding: 20px;
                font-family: "Inter", sans-serif;
                position: relative;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: auto;
                backdrop-filter: blur(10px);
            }
            .dark .player-card {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.5);
            }
            
            /* Desktop animations and effects */
            @media screen and (min-width: 768px) {
                .player-card {
                    width: 420px;
                    min-height: 500px;
                    padding: 32px;
                    border-radius: 24px;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                }
                .dark .player-card {
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
                }
                
                /* Hover effects - Desktop only */
                .player-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 35px 70px -15px rgba(0, 0, 0, 0.4);
                }
                .dark .player-card:hover {
                    box-shadow: 0 35px 70px -15px rgba(99, 102, 241, 0.3);
                }
                
                /* Playing glow effect - Desktop only */
                .player-card.is-playing {
                    animation: playerGlow 3s ease-in-out infinite;
                }
                @keyframes playerGlow {
                    0%, 100% {
                        box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.2);
                    }
                    50% {
                        box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.4), 
                                    0 0 80px -20px rgba(99, 102, 241, 0.3);
                    }
                }
                .dark .player-card.is-playing {
                    animation: playerGlowDark 3s ease-in-out infinite;
                }
                @keyframes playerGlowDark {
                    0%, 100% {
                        box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.3);
                    }
                    50% {
                        box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.5), 
                                    0 0 100px -20px rgba(99, 102, 241, 0.4);
                    }
                }
            }

            .player__top {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 16px;
                position: relative;
                z-index: 4;
            }
            
            @media screen and (min-width: 768px) {
                .player__top {
                    gap: 24px;
                }
            }

            /* Cover - Mobile (compact) */
            .player-cover {
                width: 200px;
                height: 200px;
                flex-shrink: 0;
                position: relative;
                border-radius: 16px;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Cover - Desktop (larger with effects) */
            @media screen and (min-width: 768px) {
                .player-cover {
                    width: 280px;
                    height: 280px;
                    border-radius: 20px;
                }
                
                /* Playing pulse effect - Desktop only */
                .player-cover.is-playing {
                    animation: coverPulse 2s ease-in-out infinite;
                }
                @keyframes coverPulse {
                    0%, 100% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.02);
                    }
                }
            }

            .player-cover__item {
                background-repeat: no-repeat;
                background-position: center;
                background-size: cover;
                width: 100%;
                height: 100%;
                border-radius: 16px;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            
            @media screen and (min-width: 768px) {
                .player-cover__item {
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                }
            }
            
            .player-cover__item::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 100%);
                border-radius: inherit;
            }
            
            .dark .player-cover__item {
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            }
            
            @media screen and (min-width: 768px) {
                .dark .player-cover__item {
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
                }
                
                /* Desktop hover effect */
                .player-cover:hover .player-cover__item {
                    transform: scale(1.03);
                    box-shadow: 0 25px 50px rgba(99, 102, 241, 0.4);
                }
            }

            /* Removed rotating animation */

            .player-controls {
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                margin-top: 4px;
            }
            
            @media screen and (min-width: 768px) {
                .player-controls {
                    gap: 16px;
                    margin-top: 8px;
                }
            }

            /* Mobile compact buttons */
            .player-controls__item {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                color: #64748b;
                background: rgba(255, 255, 255, 0.5);
            }
            
            /* Desktop larger buttons with animations */
            @media screen and (min-width: 768px) {
                .player-controls__item {
                    width: 48px;
                    height: 48px;
                }
                
                /* Desktop hover animations */
                .player-controls__item:hover {
                    background: rgba(99, 102, 241, 0.15);
                    color: #6366f1;
                    transform: scale(1.15) translateY(-2px);
                    box-shadow: 0 8px 16px rgba(99, 102, 241, 0.2);
                }
                
                /* Active state animation */
                .player-controls__item:active {
                    transform: scale(0.95);
                }
            }
            
            .dark .player-controls__item {
                background: rgba(255, 255, 255, 0.05);
                color: #94a3b8;
            }
            
            @media screen and (min-width: 768px) {
                .dark .player-controls__item:hover {
                    background: rgba(99, 102, 241, 0.2);
                    color: #818cf8;
                }
            }

            /* Play button - Mobile */
            .player-controls__item.-xl {
                width: 54px;
                height: 54px;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: white;
                box-shadow: 0 6px 12px rgba(99, 102, 241, 0.3);
            }
            
            /* Play button - Desktop with glow */
            @media screen and (min-width: 768px) {
                .player-controls__item.-xl {
                    width: 64px;
                    height: 64px;
                    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
                }
                
                .player-controls__item.-xl:hover {
                    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                    transform: scale(1.2) translateY(-3px);
                    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.5);
                }
                
                /* Playing pulse effect */
                .player-controls__item.-xl.is-playing {
                    animation: playButtonPulse 1.5s ease-in-out infinite;
                }
                @keyframes playButtonPulse {
                    0%, 100% {
                        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
                    }
                    50% {
                        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.5), 
                                    0 0 30px rgba(99, 102, 241, 0.3);
                    }
                }
            }

            .player-controls__item.-favorite.active {
                color: #ef4444;
            }
            .player-controls__item.-shuffle.active,
            .player-controls__item.-repeat.active {
                color: #6366f1;
                background: rgba(99, 102, 241, 0.15);
            }
            .dark .player-controls__item.-shuffle.active,
            .dark .player-controls__item.-repeat.active {
                color: #818cf8;
                background: rgba(99, 102, 241, 0.25);
            }

            /* Skip buttons styling (for -10s and +10s in Bhoot FM) */
            .player-controls__item.-skip {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                padding: 6px;
                transition: all 0.3s ease;
            }
            
            @media screen and (min-width: 768px) {
                .player-controls__item.-skip {
                    padding: 8px;
                }
                .player-controls__item.-skip:hover {
                    transform: scale(1.1);
                    background: rgba(99, 102, 241, 0.1);
                    border-radius: 8px;
                }
            }
            
            .player-controls__item.-skip .skip-label {
                font-size: 9px;
                font-weight: 600;
                opacity: 0.8;
                margin-top: -2px;
            }
            
            @media screen and (min-width: 768px) {
                .player-controls__item.-skip .skip-label {
                    font-size: 10px;
                }
            }
            
            .player-controls__item.-skip:active {
                transform: scale(0.95);
            }
            
            @keyframes skipPulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.7;
                }
            }
            
            .player-controls__item.-skip:active svg {
                animation: skipPulse 0.3s ease;
            }

            .progress {
                margin-top: 16px;
            }
            
            @media screen and (min-width: 768px) {
                .progress {
                    margin-top: 24px;
                }
            }

            .progress__top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 8px;
            }
            
            @media screen and (min-width: 768px) {
                .progress__top {
                    margin-bottom: 12px;
                }
            }

            .album-info {
                flex: 1;
                min-width: 0;
            }

            .album-info__name {
                font-size: 15px;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 2px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            @media screen and (min-width: 768px) {
                .album-info__name {
                    font-size: 18px;
                    margin-bottom: 4px;
                }
            }
            
            .dark .album-info__name {
                color: #f1f5f9;
            }

            .album-info__track {
                font-size: 12px;
                color: #64748b;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            @media screen and (min-width: 768px) {
                .album-info__track {
                    font-size: 14px;
                }
            }
            
            .dark .album-info__track {
                color: #94a3b8;
            }

            .progress__duration {
                font-size: 12px;
                font-weight: 600;
                color: #475569;
                margin-left: 12px;
            }
            
            @media screen and (min-width: 768px) {
                .progress__duration {
                    font-size: 14px;
                    margin-left: 16px;
                }
            }
            
            .dark .progress__duration {
                color: #cbd5e1;
            }

            .progress__bar {
                height: 5px;
                background: rgba(100, 116, 139, 0.2);
                border-radius: 3px;
                cursor: pointer;
                position: relative;
                overflow: hidden;
                transition: height 0.2s;
            }
            
            @media screen and (min-width: 768px) {
                .progress__bar {
                    height: 6px;
                    border-radius: 4px;
                }
                
                .progress__bar:hover {
                    height: 8px;
                }
            }
            
            .dark .progress__bar {
                background: rgba(148, 163, 184, 0.1);
            }

            .progress__current {
                height: 100%;
                background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
                border-radius: 3px;
                position: relative;
                transition: width 0.1s linear;
            }
            
            @media screen and (min-width: 768px) {
                .progress__current {
                    border-radius: 4px;
                }
                
                /* Desktop glow effect when playing */
                .is-playing .progress__current {
                    animation: progressGlow 2s ease-in-out infinite;
                }
                @keyframes progressGlow {
                    0%, 100% {
                        box-shadow: 0 0 10px rgba(99, 102, 241, 0.3);
                    }
                    50% {
                        box-shadow: 0 0 20px rgba(99, 102, 241, 0.6);
                    }
                }
            }
            
            .progress__current::after {
                content: '';
                position: absolute;
                right: -2px;
                top: 50%;
                transform: translateY(-50%);
                width: 12px;
                height: 12px;
                background: white;
                border-radius: 50%;
                box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
                opacity: 0;
                transition: opacity 0.2s;
            }
            
            @media screen and (min-width: 768px) {
                .progress__current::after {
                    width: 14px;
                    height: 14px;
                }
                
                .progress__bar:hover .progress__current::after {
                    opacity: 1;
                }
            }

            .progress__time {
                font-size: 11px;
                color: #64748b;
                margin-top: 6px;
                font-weight: 500;
            }
            
            @media screen and (min-width: 768px) {
                .progress__time {
                    font-size: 13px;
                    margin-top: 8px;
                }
            }
            
            .dark .progress__time {
                color: #94a3b8;
            }

            /* Volume Control - Mobile compact */
            .volume-control {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: 12px;
                padding: 8px 12px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 10px;
            }
            
            @media screen and (min-width: 768px) {
                .volume-control {
                    gap: 12px;
                    margin-top: 16px;
                    padding: 12px 16px;
                    border-radius: 12px;
                }
            }
            
            .dark .volume-control {
                background: rgba(255, 255, 255, 0.05);
            }

            .volume-icon {
                cursor: pointer;
                color: #64748b;
                transition: all 0.3s;
            }
            
            @media screen and (min-width: 768px) {
                .volume-icon:hover {
                    color: #6366f1;
                    transform: scale(1.1);
                }
            }
            
            .dark .volume-icon {
                color: #94a3b8;
            }
            .dark .volume-icon:hover {
                color: #818cf8;
            }

            .volume-bar {
                flex: 1;
                height: 4px;
                background: rgba(100, 116, 139, 0.2);
                border-radius: 2px;
                cursor: pointer;
                position: relative;
            }
            .dark .volume-bar {
                background: rgba(148, 163, 184, 0.1);
            }

            .volume-current {
                height: 100%;
                background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
                border-radius: 2px;
                transition: width 0.1s;
            }
            
            @media screen and (min-width: 768px) {
                /* Desktop volume glow */
                .volume-current {
                    box-shadow: 0 0 8px rgba(99, 102, 241, 0.3);
                }
            }

            .volume-percent {
                font-size: 11px;
                font-weight: 600;
                color: #64748b;
                min-width: 32px;
                text-align: right;
            }
            
            @media screen and (min-width: 768px) {
                .volume-percent {
                    font-size: 12px;
                    min-width: 35px;
                }
            }
            
            .dark .volume-percent {
                color: #94a3b8;
            }

            .close-btn {
                position: absolute;
                top: 12px;
                right: 12px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                z-index: 10;
                color: #64748b;
            }
            
            @media screen and (min-width: 768px) {
                .close-btn {
                    top: 16px;
                    right: 16px;
                    width: 36px;
                    height: 36px;
                }
                
                .close-btn:hover {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                    transform: rotate(90deg) scale(1.1);
                }
            }
            
            .dark .close-btn {
                background: rgba(255, 255, 255, 0.1);
                color: #94a3b8;
            }

            .playlist-toggle {
                position: absolute;
                top: 12px;
                left: 12px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                z-index: 10;
                color: #64748b;
            }
            
            @media screen and (min-width: 768px) {
                .playlist-toggle {
                    top: 16px;
                    left: 16px;
                    width: 36px;
                    height: 36px;
                }
                
                .playlist-toggle:hover {
                    background: rgba(99, 102, 241, 0.15);
                    color: #6366f1;
                    transform: scale(1.1);
                }
            }
            
            .dark .playlist-toggle {
                background: rgba(255, 255, 255, 0.1);
                color: #94a3b8;
            }
            .dark .playlist-toggle:hover {
                background: rgba(99, 102, 241, 0.2);
                color: #818cf8;
            }

            .playlist-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border-radius: 20px;
                padding: 50px 16px 16px;
                overflow-y: auto;
                z-index: 5;
                animation: slideIn 0.3s ease-out;
            }
            
            @media screen and (min-width: 768px) {
                .playlist-overlay {
                    border-radius: 24px;
                    padding: 60px 24px 24px;
                }
            }
            
            @keyframes slideIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .dark .playlist-overlay {
                background: rgba(15, 23, 42, 0.98);
            }

            .playlist-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.2s;
                border: 1px solid transparent;
            }
            
            @media screen and (min-width: 768px) {
                .playlist-item {
                    gap: 12px;
                    padding: 12px;
                    border-radius: 12px;
                }
                
                .playlist-item:hover {
                    background: rgba(99, 102, 241, 0.05);
                    border-color: rgba(99, 102, 241, 0.1);
                    transform: translateX(4px);
                }
            }
            
            .playlist-item.active {
                background: rgba(99, 102, 241, 0.1);
                border-color: rgba(99, 102, 241, 0.2);
            }
            .dark .playlist-item:hover {
                background: rgba(99, 102, 241, 0.1);
            }
            .dark .playlist-item.active {
                background: rgba(99, 102, 241, 0.15);
                border-color: rgba(99, 102, 241, 0.3);
            }

            .youtube-badge {
                background: #ff0000;
                color: white;
                font-size: 9px;
                padding: 3px 6px;
                border-radius: 6px;
                font-weight: 700;
                letter-spacing: 0.5px;
            }
            
            @media screen and (min-width: 768px) {
                .youtube-badge {
                    padding: 3px 7px;
                }
            }

            /* Folder Tabs */
            .folder-tabs {
                display: flex;
                gap: 8px;
                margin-bottom: 14px;
                background: rgba(0,0,0,0.04);
                padding: 4px;
                border-radius: 12px;
            }
            .dark .folder-tabs {
                background: rgba(255,255,255,0.05);
            }
            .folder-tab {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 8px 10px;
                border-radius: 9px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                color: #64748b;
                transition: all 0.25s ease;
                white-space: nowrap;
            }
            .dark .folder-tab {
                color: #94a3b8;
            }
            .folder-tab.active-songs {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: white;
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
            }
            .folder-tab.active-ghosts {
                background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%);
                color: white;
                box-shadow: 0 4px 12px rgba(76, 29, 149, 0.45);
            }
            .folder-tab.active-gojol {
                background: linear-gradient(135deg, #059669 0%, #10b981 100%);
                color: white;
                box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
            }
            .folder-tab:not(.active-songs):not(.active-ghosts):not(.active-gojol):hover {
                background: rgba(99, 102, 241, 0.08);
                color: #6366f1;
            }
            .dark .folder-tab:not(.active-songs):not(.active-ghosts):not(.active-gojol):hover {
                background: rgba(99, 102, 241, 0.15);
                color: #818cf8;
            }
            .folder-count {
                font-size: 10px;
                font-weight: 700;
                opacity: 0.75;
                background: rgba(0,0,0,0.15);
                padding: 1px 5px;
                border-radius: 8px;
            }

            /* Ghost story special styling */
            .playlist-item.ghost-item.active {
                background: rgba(76, 29, 149, 0.12);
                border-color: rgba(76, 29, 149, 0.3);
            }
            .dark .playlist-item.ghost-item.active {
                background: rgba(76, 29, 149, 0.2);
                border-color: rgba(76, 29, 149, 0.4);
            }
            .ghost-badge {
                background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%);
                color: white;
                font-size: 9px;
                padding: 3px 6px;
                border-radius: 6px;
                font-weight: 700;
                letter-spacing: 0.5px;
            }
            /* Active playing bars - ghost color */
            .ghost-bars div {
                background-color: #7c3aed !important;
            }

            /* Scrollbar styling */
            .playlist-overlay::-webkit-scrollbar {
                width: 6px;
            }
            
            @media screen and (min-width: 768px) {
                .playlist-overlay::-webkit-scrollbar {
                    width: 8px;
                }
            }
            
            .playlist-overlay::-webkit-scrollbar-track {
                background: rgba(0,0,0,0.05);
                border-radius: 4px;
            }
            .playlist-overlay::-webkit-scrollbar-thumb {
                background: rgba(99, 102, 241, 0.3);
                border-radius: 4px;
            }
            .playlist-overlay::-webkit-scrollbar-thumb:hover {
                background: rgba(99, 102, 241, 0.5);
            }
          `}),t.jsxs("div",{className:`player-card ${o?"is-playing":""}`,children:[t.jsx("div",{className:"close-btn",onClick:()=>N(!1),children:t.jsx(Re,{size:18})}),t.jsx("div",{className:"playlist-toggle",onClick:()=>E(!$),children:t.jsx(Ye,{size:18})}),$?t.jsxs("div",{className:"playlist-overlay",children:[t.jsx("div",{className:"folder-tabs",children:R.map((e,r)=>t.jsxs("div",{className:`folder-tab ${ee===e.key?["active-songs","active-ghosts","active-gojol"][r%3]:""}`,onClick:()=>te(e.key),children:[e.emoji," ",e.label,t.jsx("span",{className:"folder-count",children:g.filter(s=>s.category===e.key).length})]},e.key))}),fe.map(({track:e,globalIdx:r})=>t.jsxs("div",{className:`playlist-item ${e.category==="ghosts"?"ghost-item":""} ${x===r?"active":""}`,onClick:()=>ve(r),children:[t.jsx("img",{src:e.cover,className:"object-cover w-10 h-10 rounded-lg shadow-sm md:w-12 md:h-12",alt:e.title,loading:"lazy"}),t.jsxs("div",{className:"flex-1 min-w-0",children:[t.jsx("div",{className:"text-sm font-bold truncate text-slate-800 dark:text-white",children:e.title}),t.jsx("div",{className:"text-xs truncate text-slate-500 dark:text-slate-400",children:e.artist})]}),e.category==="ghosts"?t.jsx("span",{className:"ghost-badge",children:"👻"}):e.type==="youtube"?t.jsx("span",{className:"youtube-badge",children:"YT"}):null,x===r&&o&&t.jsxs("div",{className:`flex gap-0.5 ${e.category==="ghosts"?"ghost-bars":""}`,children:[t.jsx("div",{className:`w-1 h-3 ${e.category==="ghosts"?"bg-purple-600 dark:bg-purple-400":"bg-[#6366f1] dark:bg-[#818cf8]"} rounded-full animate-pulse`}),t.jsx("div",{className:`w-1 h-4 ${e.category==="ghosts"?"bg-purple-600 dark:bg-purple-400":"bg-[#6366f1] dark:bg-[#818cf8]"} rounded-full animate-pulse`,style:{animationDelay:"0.2s"}}),t.jsx("div",{className:`w-1 h-3 ${e.category==="ghosts"?"bg-purple-600 dark:bg-purple-400":"bg-[#6366f1] dark:bg-[#818cf8]"} rounded-full animate-pulse`,style:{animationDelay:"0.4s"}})]})]},r))]}):t.jsxs(t.Fragment,{children:[t.jsxs("div",{className:"player__top",children:[t.jsx("div",{className:`player-cover ${o?"is-playing":""}`,children:t.jsx("div",{className:"player-cover__item",style:{backgroundImage:`url(${u.cover})`}})}),t.jsxs("div",{className:"player-controls",children:[t.jsx("div",{className:`player-controls__item -shuffle ${A?"active":""}`,onClick:()=>ue(!A),title:"Shuffle",children:t.jsx(He,{size:18})}),t.jsx("div",{className:"player-controls__item",onClick:be,title:"Previous",children:t.jsx(Ae,{size:20})}),oe&&t.jsxs("div",{className:"player-controls__item -skip",onClick:ye,title:"Go back 10 seconds",children:[t.jsx(Ie,{size:18}),t.jsx("span",{className:"skip-label",children:"10s"})]}),t.jsx("div",{className:`player-controls__item -xl ${o?"is-playing":""}`,onClick:c,title:o?"Pause":"Play",children:o?t.jsx($e,{size:28}):t.jsx(Ee,{size:28,style:{marginLeft:"2px"}})}),oe&&t.jsxs("div",{className:"player-controls__item -skip",onClick:ke,title:"Skip 10 seconds",children:[t.jsx(Fe,{size:18}),t.jsx("span",{className:"skip-label",children:"10s"})]}),t.jsx("div",{className:"player-controls__item",onClick:()=>q(),title:"Next",children:t.jsx(Le,{size:20})}),t.jsx("div",{className:`player-controls__item -repeat ${z!=="off"?"active":""}`,onClick:je,title:`Repeat: ${z}`,children:z==="one"?t.jsx(Ve,{size:18}):t.jsx(Be,{size:18})})]})]}),t.jsxs("div",{className:"progress",children:[t.jsxs("div",{className:"progress__top",children:[t.jsxs("div",{className:"album-info",children:[t.jsx("div",{className:"album-info__name",children:u.title}),t.jsxs("div",{className:"album-info__track",children:[u.category==="ghosts"&&t.jsx("span",{className:"mr-1",children:"👻"}),u.category==="gojol"&&t.jsx("span",{className:"mr-1",children:"🕌"}),u.artist,d&&m&&t.jsx("span",{className:"ml-2 text-xs opacity-50",children:"(Streaming)"}),d&&!m&&t.jsx("span",{className:"ml-2 text-xs opacity-50",children:"(Loading...)"})]})]}),t.jsx("div",{className:"progress__duration",children:le})]}),t.jsx("div",{className:"progress__bar",ref:G,onClick:we,children:t.jsx("div",{className:"progress__current",style:{width:ce}})}),t.jsx("div",{className:"progress__time",children:f})]}),t.jsxs("div",{className:"volume-control",children:[t.jsx("div",{className:"volume-icon",onClick:()=>re(!y),children:y||T===0?t.jsx(Xe,{size:20}):t.jsx(Ge,{size:20})}),t.jsx("div",{className:"volume-bar",ref:X,onClick:_e,children:t.jsx("div",{className:"volume-current",style:{width:`${y?0:T}%`}})}),t.jsxs("div",{className:"volume-percent",children:[y?0:T,"%"]})]}),t.jsxs("div",{className:"flex justify-center gap-2 mt-3 md:gap-3 md:mt-4",children:[t.jsx("div",{className:`player-controls__item -favorite ${B?"active":""}`,onClick:()=>de(!B),title:"Add to favorites",children:t.jsx(De,{size:18,fill:B?"currentColor":"none"})}),t.jsx("a",{href:d?`https://www.youtube.com/watch?v=${u.youtubeId}`:"#",target:d?"_blank":"_self",className:"player-controls__item",rel:"noopener noreferrer",title:"Open in YouTube",children:t.jsx(Pe,{size:18})})]})]})]})]})]}):null};export{er as default};
