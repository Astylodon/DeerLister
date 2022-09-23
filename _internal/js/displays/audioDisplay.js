const songs = Array.from(document.getElementsByClassName("audio-preview"));
const player = document.getElementById("audio-player");
player.volume = 0.25;
if (songs.length > 0)
{
    player.src = songs[0];
}
for (const a of songs) {
    const href = a.href;
    const icon = a.getElementsByClassName("icon")[0]
    a.addEventListener("click", e => {
        e.preventDefault();
        player.src = href;
        player.play();
    });
}