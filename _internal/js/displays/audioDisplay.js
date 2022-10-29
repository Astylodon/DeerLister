let audioIndex = 0;

const songs = Array.from(document.getElementsByClassName("audio-preview"));
const player = document.getElementById("audio-player");

player.volume = 0.25;

if (songs.length > 0) {
    player.src = songs[0].href;
    document.getElementById("audio-title").innerHTML = songs[0].dataset.filename;
}

for (const a of songs) {
    a.addEventListener("click", e => {
        e.preventDefault();
        player.src = a.href;
        document.getElementById("audio-title").innerHTML = a.dataset.filename;
        player.play();
    });
}

player.addEventListener("ended", () => {
    if (audioIndex < songs.length - 1) {
        audioIndex++;
        player.src = songs[audioIndex].href;
        document.getElementById("audio-title").innerHTML = songs[audioIndex].dataset.filename;
        player.play();
    }
});