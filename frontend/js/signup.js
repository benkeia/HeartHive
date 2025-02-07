let step = 1

document.getElementById('next').addEventListener('click', () => {
  document.getElementsByClassName('step')[step - 1].style.display = 'none'
    step++
    document.getElementsByClassName('step')[step - 1].style.display = 'block'

    if (step === 3) {
        document.getElementById('next').style.display = 'none'
        document.getElementById('submit').style.display = 'block'
        }

    if (step === 0) {
        document.getElementById('previous').disabled = false
    }


})


document.getElementById('previous').addEventListener('click', () => {

    document.getElementsByClassName('step')[step - 1].style.display = 'none'
    step--
    document.getElementsByClassName('step')[step - 1].style.display = 'block'

    if (step === 2) {
        document.getElementById('next').style.display = 'block'
        

    }

    if (step === 0) {
        document.getElementById('previous').disabled = true
    }
})

