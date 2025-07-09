// Switch Account functionality removed - now handled by enhanced logout

// Function to handle automatic logout when the page is closed
function setupAutoLogout() {
  // Only set up auto-logout on pages where the user is logged in
  if (document.querySelector(".dashboard-container")) {
    // Create a session ping to keep track of active sessions
    let sessionId = Date.now().toString();
    localStorage.setItem("github_wrapped_session", sessionId);

    // Set up event listener for page unload
    window.addEventListener("beforeunload", function () {
      // Send a beacon to the logout endpoint
      navigator.sendBeacon("auto_logout.php");
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Set up automatic logout
  setupAutoLogout();

  // Language color mapping (common GitHub language colors)
  const languageColors = {
    JavaScript: "#f1e05a",
    TypeScript: "#2b7489",
    PHP: "#4F5D95",
    HTML: "#e34c26",
    CSS: "#563d7c",
    Python: "#3572A5",
    Java: "#b07219",
    "C#": "#178600",
    "C++": "#f34b7d",
    Ruby: "#701516",
    Go: "#00ADD8",
    Swift: "#ffac45",
    Kotlin: "#F18E33",
    Rust: "#dea584",
    Dart: "#00B4AB",
  };

  // Generate colors for languages that don't have a predefined color
  function getLanguageColor(language) {
    if (languageColors[language]) {
      return languageColors[language];
    }

    // Generate a pseudo-random color based on the language name
    let hash = 0;
    for (let i = 0; i < language.length; i++) {
      hash = language.charCodeAt(i) + ((hash << 5) - hash);
    }

    let color = "#";
    for (let i = 0; i < 3; i++) {
      const value = (hash >> (i * 8)) & 0xff;
      color += ("00" + value.toString(16)).substr(-2);
    }

    return color;
  }

  // Generate background colors for all languages
  const backgroundColors = languageLabels.map((lang) => getLanguageColor(lang));

  // Language Chart
  if (document.getElementById("languageChart")) {
    const languageCtx = document
      .getElementById("languageChart")
      .getContext("2d");
    new Chart(languageCtx, {
      type: "doughnut",
      data: {
        labels: languageLabels,
        datasets: [
          {
            data: languageData,
            backgroundColor: backgroundColors,
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: {
              color: "#c9d1d9",
              font: {
                size: 12,
              },
              padding: 20,
            },
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return `${context.label}: ${context.raw}%`;
              },
            },
          },
        },
        cutout: "70%",
      },
    });
  }

  // Weekly Activity Chart
  if (document.getElementById("weeklyChart")) {
    const weeklyCtx = document.getElementById("weeklyChart").getContext("2d");
    new Chart(weeklyCtx, {
      type: "bar",
      data: {
        labels: [
          "Sunday",
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday",
        ],
        datasets: [
          {
            label: "Commits",
            data: dayOfWeekData,
            backgroundColor: "#6e40c9",
            borderRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "#c9d1d9",
            },
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#c9d1d9",
            },
          },
        },
      },
    });
  }

  // Hourly Activity Chart
  if (document.getElementById("hourlyChart")) {
    const hourlyCtx = document.getElementById("hourlyChart").getContext("2d");

    // Format hour labels (0-23 to 12am-11pm)
    const hourLabels = Array.from({ length: 24 }, (_, i) => {
      if (i === 0) return "12am";
      if (i === 12) return "12pm";
      return i < 12 ? `${i}am` : `${i - 12}pm`;
    });

    new Chart(hourlyCtx, {
      type: "line",
      data: {
        labels: hourLabels,
        datasets: [
          {
            label: "Commits",
            data: hourOfDayData,
            backgroundColor: "rgba(46, 160, 67, 0.2)",
            borderColor: "#2ea043",
            borderWidth: 2,
            pointBackgroundColor: "#2ea043",
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.3,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "#c9d1d9",
            },
          },
          x: {
            grid: {
              display: false,
            },
            ticks: {
              color: "#c9d1d9",
              maxRotation: 45,
              minRotation: 45,
              callback: function (value, index) {
                // Show only every 3 hours to avoid crowding
                return index % 3 === 0 ? hourLabels[index] : "";
              },
            },
          },
        },
      },
    });
  }

  // Activity Chart (combined weekly and hourly)
  if (document.getElementById("activityChart")) {
    const activityCtx = document
      .getElementById("activityChart")
      .getContext("2d");

    // Create a 2D array for the heatmap data
    // [day][hour] = commit count
    const heatmapData = [];
    const days = [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ];

    // Initialize with zeros
    for (let i = 0; i < 7; i++) {
      heatmapData.push(Array(24).fill(0));
    }

    // Populate with random data for now (this would be replaced with real data)
    for (let day = 0; day < 7; day++) {
      for (let hour = 0; hour < 24; hour++) {
        // Use the day and hour data to approximate a distribution
        // In a real implementation, you'd have actual [day][hour] data
        heatmapData[day][hour] =
          (dayOfWeekData[day] / 10) * (hourOfDayData[hour] / 5);
      }
    }

    // Flatten the 2D array for Chart.js
    const flatData = [];
    for (let day = 0; day < 7; day++) {
      for (let hour = 0; hour < 24; hour++) {
        flatData.push({
          x: hour,
          y: day,
          v: heatmapData[day][hour],
        });
      }
    }

    new Chart(activityCtx, {
      type: "scatter",
      data: {
        datasets: [
          {
            label: "Activity",
            data: flatData,
            backgroundColor: (context) => {
              const value = context.raw.v;
              const alpha = Math.min(0.2 + value / 10, 1); // Scale opacity based on value
              return `rgba(46, 160, 67, ${alpha})`;
            },
            pointRadius: 8,
            pointHoverRadius: 10,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const point = context.raw;
                const hour = point.x;
                const day = days[point.y];
                const hourLabel =
                  hour < 12
                    ? hour === 0
                      ? "12am"
                      : `${hour}am`
                    : hour === 12
                    ? "12pm"
                    : `${hour - 12}pm`;

                return `${day} at ${hourLabel}: ~${Math.round(
                  point.v
                )} commits`;
              },
            },
          },
        },
        scales: {
          y: {
            min: -0.5,
            max: 6.5,
            ticks: {
              callback: function (value) {
                return days[value];
              },
              color: "#c9d1d9",
            },
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
          },
          x: {
            min: -0.5,
            max: 23.5,
            ticks: {
              callback: function (value) {
                if (value % 6 === 0) {
                  return value < 12
                    ? value === 0
                      ? "12am"
                      : `${value}am`
                    : value === 12
                    ? "12pm"
                    : `${value - 12}pm`;
                }
                return "";
              },
              color: "#c9d1d9",
            },
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
          },
        },
      },
    });
  }

  // Share functionality
  const shareBtn = document.getElementById("shareBtn");
  const shareContainer = document.getElementById("shareContainer");
  const closeShareBtn = document.getElementById("closeShareBtn");
  const downloadBtn = document.getElementById("downloadBtn");
  const twitterBtn = document.getElementById("twitterBtn");
  const sharePreview = document.getElementById("sharePreview");

  if (shareBtn && shareContainer) {
    shareBtn.addEventListener("click", () => {
      // Clone the dashboard for the preview
      const dashboard = document.querySelector(".dashboard-container");
      const clone = dashboard.cloneNode(true);

      // Remove the actions section and share container from the clone
      const actionsToRemove = clone.querySelector(".actions");
      if (actionsToRemove) {
        actionsToRemove.remove();
      }

      // Add a watermark
      const watermark = document.createElement("div");
      watermark.style.textAlign = "center";
      watermark.style.marginTop = "20px";
      watermark.style.padding = "10px";
      watermark.style.borderTop = "1px solid #30363d";
      watermark.innerHTML =
        "<p>Generated with GitHub Wrapped | github-wrapped.com</p>";
      clone.appendChild(watermark);

      // Display the preview
      sharePreview.innerHTML = "";
      sharePreview.appendChild(clone);
      shareContainer.style.display = "flex";

      // Adjust the clone's styling for the preview
      clone.style.padding = "10px";
      clone.style.maxWidth = "100%";

      // Hide any buttons in the clone
      const buttons = clone.querySelectorAll("button");
      buttons.forEach((button) => (button.style.display = "none"));
    });

    closeShareBtn.addEventListener("click", () => {
      shareContainer.style.display = "none";
    });

    downloadBtn.addEventListener("click", () => {
      html2canvas(sharePreview.firstChild).then((canvas) => {
        const link = document.createElement("a");
        link.download = `github-wrapped-${username}.png`;
        link.href = canvas.toDataURL("image/png");
        link.click();
      });
    });

    twitterBtn.addEventListener("click", () => {
      html2canvas(sharePreview.firstChild).then((canvas) => {
        // In a real app, you'd upload this image to your server and get a URL
        // For now, we'll just open a tweet composer with text
        const tweetText = `Check out my GitHub Wrapped! I made ${
          stats?.total_commits || "many"
        } commits this year and my top language is ${
          languageLabels[0] || "awesome"
        }. #GitHubWrapped`;
        const tweetUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(
          tweetText
        )}`;
        window.open(tweetUrl, "_blank");
      });
    });
  }
});
