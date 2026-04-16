<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>YE.LIKESYOU.ORG - UNRELEASED YE</title>
    <style>
        :root { 
            --bg: #000; 
            --text: #fff; 
            --line: #1a1a1a;
            --accent: #fff;
        }

        * { 
            box-sizing: border-box; 
            border-radius: 0 !important; 
            cursor: crosshair;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body { 
            background: var(--bg); 
            color: var(--text); 
            font-family: "Helvetica", Arial, sans-serif; 
            text-transform: uppercase; 
            margin: 0; 
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* TOP NAV - REDUCED */
        .top-nav {
            position: fixed; 
            top: 0; 
            width: 100%; 
            padding: 15px 20px;
            border-bottom: 1px solid var(--line); 
            display: flex; 
            justify-content: space-between;
            font-weight: 900; 
            font-size: 0.6rem; 
            letter-spacing: 4px;
            z-index: 100;
        }

        /* COMPACT LAYOUT */
        .layout { 
            display: grid; 
            grid-template-columns: 320px 500px; 
            height: 600px; 
            border: 1px solid var(--line);
            background: #050505;
        }

        /* SIDEBAR - SCROLLABLE */
        .list { 
            border-right: 1px solid var(--line); 
            overflow-y: scroll;
            scrollbar-width: none;
            background: #000;
        }
        .list::-webkit-scrollbar { display: none; }

        .track-item {
            padding: 20px; 
            border-bottom: 1px solid var(--line);
            cursor: pointer; 
            opacity: 0.3; 
            font-weight: 900; 
            font-size: 1.2rem; 
            letter-spacing: -1px;
        }

        .track-item:hover { 
            opacity: 0.7; 
            padding-left: 25px;
            background: #0a0a0a;
        }

        .track-item.active { 
            opacity: 1; 
            background: #fff;
            color: #000;
        }

        /* VIEWER */
        .viewer { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            padding: 40px;
            position: relative;
        }

        /* ART - SMALLER & ANIMATED */
        .art-frame {
            width: 300px; 
            height: 300px; 
            background: #000;
            border: 1px solid var(--line); 
            margin-bottom: 30px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #cover-art { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            opacity: 0;
            transform: scale(1.1);
        }

        #cover-art.loaded {
            opacity: 1;
            transform: scale(1);
        }

        /* PLAYER UI */
        .player-ui { 
            width: 300px; 
        }

        #active-title { 
            font-size: 0.65rem; 
            font-weight: 900; 
            margin-bottom: 10px; 
            letter-spacing: 2px;
            height: 15px;
        }

        /* PROGRESS */
        .progress-bar { 
            width: 100%; 
            height: 2px; 
            background: #1a1a1a; 
            cursor: pointer; 
            margin-bottom: 20px; 
        }

        #progress-fill { 
            width: 0%; 
            height: 100%; 
            background: #fff; 
            transition: width 0.1s linear;
        }

        /* SQUARE BTN */
        .btn-main { 
            background: #fff; 
            border: none; 
            width: 50px; 
            height: 50px; 
            cursor: pointer; 
            display: flex;
            align-items: center;
            justify-content: center;
            float: right;
        }

        .btn-main:hover { 
            transform: scale(1.05);
            background: #eee;
        }

        .btn-main svg {
            width: 20px;
            height: 20px;
            fill: #000;
        }

        .stamp { 
            position: absolute; 
            bottom: 15px; 
            font-size: 6px; 
            opacity: 0.2; 
            text-align: center;
            width: 80%;
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="top-nav">
    <span>YE.LIKESYOU.ORG</span>
    <span>MADE BY REDRETEP</span>
</div>

<div class="layout">
    <div class="list">
        <?php
        $dir = "./audio/";
        if (is_dir($dir)) {
            $files = glob($dir . "*.mp3");
            if ($files) {
                foreach ($files as $file) {
                    $base = pathinfo($file, PATHINFO_FILENAME);
                    $cover = file_exists("$dir$base-cover.png") ? "$dir$base-cover.png" : "";
                    echo "<div class='track-item' onclick='loadTrack(\"$file\", \"$base\", \"$cover\", this)'>$base</div>";
                }
            }
        }
        ?>
    </div>

    <div class="viewer">
        <div class="art-frame">
            <img id="cover-art" src="">
        </div>

        <div class="player-ui">
            <div id="active-title">SELECT_TRACK</div>
            <div class="progress-bar" id="timeline" onclick="seek(event)">
                <div id="progress-fill"></div>
            </div>
            
            <button class="btn-main" onclick="togglePlay()" id="play-pause">
                <svg id="svg-icon" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </button>
        </div>
        
        <audio id="player" ontimeupdate="update()"></audio>
        <div class="stamp">THIS SITE IS ONLY CONTAINS UNOFFICIAL REUPLOADS OF YE’S OLD OR REMOVED TRACKS. ALL RIGHTS BELONG TO YE AND THE ORIGINAL CREATORS.</div>
    </div>
</div>

<script>
const audio = document.getElementById('player');
const btn = document.getElementById('play-pause');
const icon = document.getElementById('svg-icon');
const fill = document.getElementById('progress-fill');
const img = document.getElementById('cover-art');

const playIcon = '<path d="M8 5v14l11-7z"/>';
const pauseIcon = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';

function loadTrack(path, title, cover, el) {
    document.querySelectorAll('.track-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');

    img.classList.remove('loaded');
    
    setTimeout(() => {
        audio.src = path;
        audio.play();
        icon.innerHTML = pauseIcon;
        document.getElementById('active-title').innerText = title;

        if(cover) {
            img.src = cover;
            img.classList.add('loaded');
        }
    }, 200);
}

function togglePlay() {
    if(!audio.src) return;
    if (audio.paused) {
        audio.play();
        icon.innerHTML = pauseIcon;
    } else {
        audio.pause();
        icon.innerHTML = playIcon;
    }
}

function update() {
    if(!audio.duration) return;
    const percent = (audio.currentTime / audio.duration) * 100;
    fill.style.width = percent + "%";
}

function seek(e) {
    if(!audio.src) return;
    const rect = document.getElementById('timeline').getBoundingClientRect();
    const x = e.clientX - rect.left;
    audio.currentTime = (x / rect.width) * audio.duration;
}
</script>

</body>
</html>
