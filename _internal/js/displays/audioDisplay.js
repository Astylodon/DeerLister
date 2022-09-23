const musics = Array.from(document.getElementsByClassName("audio-preview"));
const player = document.getElementById("audio-player");
player.volume = 0.25;
if (musics.length > 0)
{
    player.src = musics[0];
}
for (const a of musics) {
    const href = a.href;
    a.addEventListener("click", e => {
        e.preventDefault();
        player.src = href;
        player.play();
    });
}