let audioIndex = 0;

const songs = Array.from(document.getElementsByClassName("audio-preview"));
const player = document.getElementById("audio-player");

player.volume = 0.25;

function playSong(index, startAuto = true) {
    player.src = songs[index].dataset.song;
    document.getElementById("audio-title").innerHTML = songs[index].dataset.filename;
    if (startAuto) player.play();
}

if (songs.length > 0) {
    player.src = songs[0].dataset.song;
    document.getElementById("audio-title").innerHTML = songs[0].dataset.filename;
}

for (let i = 0; i < songs.length; i++) {
    let curr = i;

    songs[i].addEventListener("click", e => {
        audioIndex = curr;
        playSong(audioIndex);
    });
    songs[i].querySelector(".share").addEventListener("click", e => {
        e.stopPropagation();

        const filename = songs[curr].dataset.filename
        const share = encodeURI(filename)
        showShare(createShareUrl(share))
    });
    songs[i].querySelector(".download").addEventListener("click", e => {
        e.stopPropagation();
    });
}

player.addEventListener("ended", () => {
    if (audioIndex < songs.length - 1) {
        audioIndex++;
        playSong(audioIndex);
    }
});

let shareParams = new URLSearchParams(document.location.search)
let share = shareParams.get("share")
if (share) {
    for (let i = 0; i < songs.length; i++) {
        audioIndex = i;
        playSong(audioIndex, false);
    }
}