/* eslint-disable */

let resizeListenerBound = false;

function getNavPills() {
  return Array.from(document.querySelectorAll(".nav-pills")).filter(
    (item) => item.isConnected
  );
}

function getEventTarget(e) {
  e = e || window.event;
  return e.target || e.srcElement;
}

function createMovingTab(item, sourceLink) {
  if (!sourceLink) {
    return null;
  }

  const existing = item.querySelector(".moving-tab");
  if (existing) {
    existing.remove();
  }

  const moving_div = document.createElement("div");
  const tab = sourceLink.cloneNode();
  tab.innerHTML = "-";

  moving_div.classList.add("moving-tab", "position-absolute", "nav-link");
  moving_div.appendChild(tab);
  item.appendChild(moving_div);

  moving_div.style.padding = "0px";
  moving_div.style.transition = ".5s ease";

  return moving_div;
}

function positionMovingTab(item, moving_div) {
  if (!moving_div || !item.isConnected) {
    return;
  }

  const activeLink = item.querySelector(".nav-link.active");
  const li = activeLink ? activeLink.parentElement : item.querySelector("li");

  if (!li) {
    return;
  }

  const list = li.closest("ul");
  if (!list) {
    return;
  }

  const nodes = Array.from(list.children);
  const index = nodes.indexOf(li) + 1;
  let sum = 0;

  if (item.classList.contains("flex-column")) {
    for (let j = 1; j <= nodes.indexOf(li); j++) {
      const sibling = item.querySelector("li:nth-child(" + j + ")");
      if (sibling) {
        sum += sibling.offsetHeight;
      }
    }
    const current = item.querySelector("li:nth-child(" + index + ")");
    moving_div.style.transform = "translate3d(0px," + sum + "px, 0px)";
    moving_div.style.width = (current ? current.offsetWidth : 0) + "px";
    moving_div.style.height = current ? current.offsetHeight + "px" : "0px";
  } else {
    for (let j = 1; j <= nodes.indexOf(li); j++) {
      const sibling = item.querySelector("li:nth-child(" + j + ")");
      if (sibling) {
        sum += sibling.offsetWidth;
      }
    }
    const current = item.querySelector("li:nth-child(" + index + ")");
    moving_div.style.transform = "translate3d(" + sum + "px, 0px, 0px)";
    moving_div.style.width = (current ? current.offsetWidth : 0) + "px";
  }
}

function initNavs() {
  getNavPills().forEach(function (item) {
    const first_li = item.querySelector("li:first-child .nav-link");
    if (!first_li) {
      return;
    }

    const moving_div = createMovingTab(item, first_li);
    if (!moving_div) {
      return;
    }

    const firstItem = item.querySelector("li:nth-child(1)");
    moving_div.style.width = (firstItem ? firstItem.offsetWidth : 0) + "px";
    moving_div.style.transform = "translate3d(0px, 0px, 0px)";

    item.onmouseover = function (event) {
      const target = getEventTarget(event);
      const li = target.closest("li");
      if (!li) {
        return;
      }

      const list = li.closest("ul");
      if (!list) {
        return;
      }

      const nodes = Array.from(list.children);
      const index = nodes.indexOf(li) + 1;
      const navLink = item.querySelector(
        "li:nth-child(" + index + ") .nav-link"
      );

      if (!navLink) {
        return;
      }

      navLink.onclick = function () {
        const tab = item.querySelector(".moving-tab");
        if (!tab) {
          return;
        }

        let sum = 0;
        if (item.classList.contains("flex-column")) {
          for (let j = 1; j <= nodes.indexOf(li); j++) {
            const sibling = item.querySelector("li:nth-child(" + j + ")");
            if (sibling) {
              sum += sibling.offsetHeight;
            }
          }
          const current = item.querySelector("li:nth-child(" + index + ")");
          tab.style.transform = "translate3d(0px," + sum + "px, 0px)";
          tab.style.height = current ? current.offsetHeight + "px" : "0px";
        } else {
          for (let j = 1; j <= nodes.indexOf(li); j++) {
            const sibling = item.querySelector("li:nth-child(" + j + ")");
            if (sibling) {
              sum += sibling.offsetWidth;
            }
          }
          const current = item.querySelector("li:nth-child(" + index + ")");
          tab.style.transform = "translate3d(" + sum + "px, 0px, 0px)";
          tab.style.width = (current ? current.offsetWidth : 0) + "px";
        }
      };
    };
  });
}

function onResize() {
  getNavPills().forEach(function (item) {
    const activeLink =
      item.querySelector(".nav-link.active") ||
      item.querySelector("li:first-child .nav-link");
    const moving_div = createMovingTab(item, activeLink);
    positionMovingTab(item, moving_div);
  });

  if (window.innerWidth < 991) {
    getNavPills().forEach(function (item) {
      if (!item.classList.contains("flex-column")) {
        item.classList.remove("flex-row");
        item.classList.add("flex-column", "on-resize");
        const moving_div = item.querySelector(".moving-tab");
        positionMovingTab(item, moving_div);
      }
    });
  } else {
    getNavPills().forEach(function (item) {
      if (item.classList.contains("on-resize")) {
        item.classList.remove("flex-column", "on-resize");
        item.classList.add("flex-row");
        const moving_div = item.querySelector(".moving-tab");
        positionMovingTab(item, moving_div);
      }
    });
  }
}

export default function setNavPills() {
  setTimeout(function () {
    initNavs();
  }, 100);

  if (!resizeListenerBound) {
    window.addEventListener("resize", onResize);
    resizeListenerBound = true;
  }

  if (window.innerWidth < 991) {
    getNavPills().forEach(function (item) {
      if (item.classList.contains("flex-row")) {
        item.classList.remove("flex-row");
        item.classList.add("flex-column", "on-resize");
      }
    });
  }
}
