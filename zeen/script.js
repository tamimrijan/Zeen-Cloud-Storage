document.addEventListener("DOMContentLoaded", () => {
    const deleteButtons = document.querySelectorAll('button[type="submit"]')
    deleteButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        const itemName = this.parentElement.querySelector('input[name="delete"]').value
        if (!confirm(`Are you sure you want to delete "${itemName}"?`)) {
          e.preventDefault()
        }
      })
    })
  
    // Dark mode toggle functionality
    const darkModeToggle = document.getElementById("darkModeToggle")
    const body = document.body
  
    // Check for saved dark mode preference
    if (localStorage.getItem("darkMode") === "enabled") {
      body.classList.add("dark-mode")
    }
  
    darkModeToggle.addEventListener("click", () => {
      body.classList.toggle("dark-mode")
  
      // Save dark mode preference
      if (body.classList.contains("dark-mode")) {
        localStorage.setItem("darkMode", "enabled")
      } else {
        localStorage.setItem("darkMode", "disabled")
      }
    })
  })
  
  