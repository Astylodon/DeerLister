const template = document.getElementById("filepreview")

// register a click handler for each file link
document.querySelectorAll(".flex-container > a").forEach(link => link.addEventListener("click", fileClicked))

// prevents clicking modal to dispatch modal close
document.querySelector(".modal-body").addEventListener("click", event => event.stopPropagation())

// close modal while clicking outside it
document.querySelector(".modal").addEventListener("click", function(event) {
    document.querySelector(".modal-body").innerHTML = ""
    event.currentTarget.style.display = "none"
})

function fileClicked(event) {
    const target = event.currentTarget

    const file = target.dataset.preview
    const filename = target.dataset.filename

    // check if file is previewable
    if (file) {
        event.preventDefault()

        // fetch file preview
        fetch("/?preview=" + encodeURIComponent(file))
            .then(response => {
                if (!response.ok) {
                    throw "Failed to preview file: " + response.statusText
                }

                return response.text()
            })
            .then(content => {
                showFileModal(target, filename, content);
                document.querySelectorAll('pre code').forEach((el) => {
                    hljs.highlightElement(el);
                });
            })
    }
}

function showFileModal(target, filename, content) {
    // clone template
    const clone = template.content.cloneNode(true)

    // fill out
    clone.querySelector("h2").innerText = filename
    clone.querySelector("div").innerHTML = content
    
    clone.querySelector("a").href = target.href
    
    // add into modal and show it
    document.querySelector(".modal-body").appendChild(clone)
    document.getElementById("modal").style.display = "block"
}
