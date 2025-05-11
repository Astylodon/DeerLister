const template = document.getElementById("filepreview")

// register a click handler for each file link
document.querySelectorAll("#file-display a").forEach(link => link.addEventListener("click", fileClicked))

// prevents clicking modal to dispatch modal close
document.querySelector(".modal-body").addEventListener("click", event => event.stopPropagation())

// close modal while clicking outside it
document.querySelector(".modal").addEventListener("click", function(event) {
    document.querySelector(".modal-body").innerHTML = ""
    event.currentTarget.style.display = "none"
})

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

    document.getElementById("share").addEventListener("click", _ => {
        const nodes = document.querySelectorAll(".path > a")
        const dir = nodes[nodes.length - 1]
        const url = `${window.location.origin}${window.location.pathname}?dir=${dir.innerHTML}&share=${shareName}`
        if (navigator.share)
        {
            navigator.share({url: url});
        }
        else
        {
            window.prompt("Copy to share", url)
        }
    })
}
