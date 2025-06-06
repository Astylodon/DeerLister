const template = document.getElementById("filepreview")
const baseTitle = document.title;
const baseUrl = document.location.href;

// register a click handler for each file link
document.querySelectorAll("#file-display a").forEach(link => link.addEventListener("click", fileClicked))

// prevents clicking modal to dispatch modal close
document.querySelector(".modal-body").addEventListener("click", event => event.stopPropagation())

// close modal while clicking outside it
document.querySelector(".modal").addEventListener("click", function(event) {
    document.querySelector(".modal-body").innerHTML = ""
    event.currentTarget.style.display = "none"

    let url = new URL(document.location.href)
    let dirParam = url.searchParams.get("dir")
    let finalUrl = document.location.origin + document.location.pathname;
    if (dirParam) {
        const split = dirParam.split('/')
        document.title = split[split.length - 1]
        finalUrl += `?dir=${dirParam}`
    } else {
        document.title = "Home"
    }
    window.history.pushState({"html": document.html,"pageTitle": document.title},"", finalUrl)
})

window.addEventListener("popstate", (e) => {
    if(e.state) {
        console.log(e.state);
        document.getElementById("content").innerHTML = e.state.html;
        document.title = e.state.pageTitle;
    }
});

const prev = document.getElementById("selected-preview")
if (prev != undefined) {
    showFile(prev.href, prev.dataset.preview, prev.dataset.filename, encodeURI(prev.dataset.filename))
}

function fileClicked(event) {
    const target = event.currentTarget

    const file = target.dataset.preview
    const filename = target.dataset.filename
    const share = encodeURI(filename)

    // check if file is previewable
    if (file) {
        event.preventDefault()

        showFile(target.href, file, filename, share)
    }
}

function showFile(href, file, filename, shareName) {

    // fetch file preview
    fetch("/?preview=" + encodeURIComponent(file))
        .then(response => {
            if (!response.ok) {
                throw "Failed to preview file: " + response.statusText
            }

            return response.text()
        })
        .then(content => {
            showFileModal(href, filename, content, shareName);
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        })
}

function showFileModal(href, filename, content, shareName) {
    // clone template
    const clone = template.content.cloneNode(true)

    // fill out
    clone.querySelector("h2").innerText = filename
    clone.querySelector("div").innerHTML = content
    
    clone.querySelector("a").href = href
    
    // add into modal and show it
    document.querySelector(".modal-body").innerHTML = ""
    document.querySelector(".modal-body").appendChild(clone)
    document.getElementById("modal").style.display = "block"

    let shareParams = new URLSearchParams(document.location.search)
    shareParams.set("share", shareName)
    const shareUrl = `${window.location.origin}${window.location.pathname}?${shareParams.toString()}`

    document.title = filename
    window.history.pushState({"html": document.html,"pageTitle": filename},"", shareUrl)

    document.getElementById("share").addEventListener("click", _ => {
        if (navigator.share)
        {
            navigator.share({url: shareUrl});
        }
        else
        {
            window.prompt("Copy to share", shareUrl)
        }
    })
}
