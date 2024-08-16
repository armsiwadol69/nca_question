// Utility function to generate random colors
function getRandomColor() {
  const r = Math.floor(Math.random() * 255);
  const g = Math.floor(Math.random() * 255);
  const b = Math.floor(Math.random() * 255);
  return `rgba(${r}, ${g}, ${b}, 0.4)`;
}

let startDate = moment().startOf("day").format("YYYY-MM-DD");
let endDate = moment().endOf("day").format("YYYY-MM-DD");

console.log(startDate);
console.log(endDate);

let chartOverall;
let chartOverallData = { labels: [], datasets: [] };

let chartDepSec;
let chartDepSecData = { labels: [], datasets: [] };

let chartTotalByDepSec;
let chartTotalByDepSecData = { labels: [], datasets: [] };

function initializeReportChart() {
  Chart.register(ChartDataLabels);
  const ctx1 = document.getElementById("chart_overall").getContext("2d");
  chartOverall = new Chart(ctx1, {
    type: "bar",
    data: chartOverallData,
    options: {
      scales: {
        x: {
          stacked: false,
          grid: {
            display: false,
          },
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "จำนวนครั้งที่พบปัญหา",
          },
        },
      },
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
          callbacks: {
            label: function (tooltipItem) {
              const value = tooltipItem.raw; // Get the value for the current label
              if (value === 0) {
                return null; // Return null to hide the label if value is 0
              }
              return `${tooltipItem.dataset.label}: ${value}`; // Display the label with its value
            },
          },
        },
        datalabels: {
          formatter: (value, context) => {
            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
            let percentage = ((value * 100) / sum).toFixed(2) + "%";
            // return `${percentage}\n(${value})`;
            // ${context.chart.data.labels[context.dataIndex]}\n${percentage}\n(${value})
            return context.dataset.data[context.dataIndex] !== 0
              ? `${value}`
              : "";
          },
          color: "#000",
          font: {
            family: "Prompt",
            weight: "normal",
            titlesize: 10,
          },
          textAlign: "center",
        },
      },
    },
  });

  const ctx2 = document.getElementById("chart_bydepsec").getContext("2d");
  chartDepSec = new Chart(ctx2, {
    type: "bar",
    data: chartDepSecData,
    options: {
      scales: {
        x: {
          stacked: false,
          grid: {
            display: false,
          },
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "จำนวนครั้งที่พบปัญหา",
          },
        },
      },
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
        datalabels: {
          formatter: (value, context) => {
            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
            let percentage = ((value * 100) / sum).toFixed(2) + "%";
            // return `${percentage}\n(${value})`;
            // ${context.chart.data.labels[context.dataIndex]}\n${percentage}\n(${value})
            return context.dataset.data[context.dataIndex] !== 0
              ? `${context.chart.data.labels[context.dataIndex]}\n${percentage} (${value})`
              : "";
          },
          color: "#000",
          font: {
            family: "Prompt",
            weight: "normal",
            titlesize: 10,
          },
          textAlign: "center",
        },
      },
    },
  });

  const ctx3 = document.getElementById("chart_totalByDepSec").getContext("2d");
  chartTotalByDepSec = new Chart(ctx3, {
    type: "bar",
    data: chartDepSecData,
    options: {
      scales: {
        x: {
          stacked: false,
          grid: {
            display: false,
          },
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "จำนวนครั้งที่พบปัญหา",
          },
        },
      },
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
        datalabels: {
          formatter: (value, context) => {
            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
            let percentage = ((value * 100) / sum).toFixed(2) + "%";
            // return `${percentage}\n(${value})`;
            // ${context.chart.data.labels[context.dataIndex]}\n${percentage}\n(${value})
            return context.dataset.data[context.dataIndex] !== 0
              ? `${context.chart.data.labels[context.dataIndex]}\n${percentage} (${value})`
              : "";
          },
          color: "#000",
          font: {
            family: "Prompt",
            weight: "normal",
            size: 10,
          },
          textAlign: "center",
        },
      },
    },
  });
}

function generateChartData(sections) {
  const labels = new Set();
  sections.forEach((section) => {
    section.most_happening.forEach((happening) => {
      labels.add(happening.title);
    });
  });

  const labelsArray = Array.from(labels);

  const datasets = sections.map((section) => {
    const data = labelsArray.map((label) => {
      const happening = section.most_happening.find((h) => h.title === label);
      return happening ? happening.COUNT : 0;
    });

    return {
      label: `${section.section_name}`,
      data: data,
      backgroundColor: getRandomColor(),
      borderColor: getRandomColor(),
      borderWidth: 1,
    };
  });

  const dt = {
    labels: labelsArray,
    datasets: datasets,
  };
  return dt;
}

async function fetchChartDataOverAll() {
  try {
    const data = await new Promise((resolve, reject) => {
      $.post("../class/apiform.php", {
        method: "generateTopStatOfDepSecReport",
        startDate: startDate,
        endDate: endDate,
      })
        .done((response) => resolve(response))
        .fail((jqXHR, textStatus, errorThrown) =>
          reject(new Error(`Request failed: ${textStatus}, ${errorThrown}`))
        );
    });

    return data;
  } catch (error) {
    console.error("Failed to fetch chart data:", error);
    throw error; // Optionally rethrow the error if you want it to be handled further up the call stack
  }
}

async function fetchChartDataDepSec() {
  const depSec = $("#par_depsec").val();
  try {
    const data = await new Promise((resolve, reject) => {
      $.post("../class/apiform.php", {
        method: "generateDepSecReport",
        section: depSec,
        startDate: startDate,
        endDate: endDate,
      })
        .done((response) => resolve(response))
        .fail((jqXHR, textStatus, errorThrown) =>
          reject(new Error(`Request failed: ${textStatus}, ${errorThrown}`))
        );
    });

    return data;
  } catch (error) {
    console.error("Failed to fetch chart data:", error);
    throw error; // Optionally rethrow the error if you want it to be handled further up the call stack
  }
}

async function fetchChartDataTotalByDepSec() {
  try {
    const data = await new Promise((resolve, reject) => {
      $.post("../class/apiform.php", {
        method: "generateCountSumByDepSec",
        startDate: startDate,
        endDate: endDate,
      })
        .done((response) => resolve(response))
        .fail((jqXHR, textStatus, errorThrown) =>
          reject(new Error(`Request failed: ${textStatus}, ${errorThrown}`))
        );
    });
    return data;
  } catch (error) {
    console.error("Failed to fetch chart data:", error);
    throw error; // Optionally rethrow the error if you want it to be handled further up the call stack
  }
}

async function updateChartByDateChange() {
  overallData = await fetchChartDataOverAll();
  chartOverall.data = generateChartData(overallData);
  chartOverall.update();
}

async function updateChartByDepSecChange() {
  chartDepSecData = await fetchChartDataDepSec();
  chartDepSec.data = convertToChartDepSecData(chartDepSecData);
  chartDepSec.update();
}

async function updateChartByTotalDepSecChange() {
  chartTotalByDepSecData = await fetchChartDataTotalByDepSec();
  chartTotalByDepSec.data = convertToChartTotalDepSecData(chartTotalByDepSecData);
  console.log(chartTotalByDepSec.data);
  chartTotalByDepSec.update();
}

function initializeDateRange() {
  $(function () {
    $('input[name="daterange"]').daterangepicker(
      {
        // opens: "left",
        ranges: {
          วันนี้: [moment(), moment()],
          เมื่อวาน: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "ล่าสุด 7 วัน": [moment().subtract(6, "days"), moment()],
          "ล่าสุด 30 วัน": [moment().subtract(29, "days"), moment()],
          เดือนนี้: [moment().startOf("month"), moment().endOf("month")],
          เดือนที่แล้ว: [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
          ปีนี้: [moment().startOf("years"), moment().endOf("years")],
          ปีที่แล้ว: [
            moment().subtract(1, "years").startOf("years"),
            moment().subtract(1, "years").endOf("years"),
          ],
        },
        locale: {
          format: "DD/MM/YYYY",
          applyLabel: "ยืนยัน",
          cancelLabel: "ยกเลิก",
          customRangeLabel: "กำหนดเอง",
          daysOfWeek: ["อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"],
          monthNames: [
            "มกราคม",
            "กุมภาพันธ์",
            "มีนาคม",
            "เมษายน",
            "พฤษภาคม",
            "มิถุนายน",
            "กรกฎาคม",
            "สิงหาคม",
            "กันยายน",
            "ตุลาคม",
            "พฤศจิกายน",
            "ธันวาคม",
          ],
          linkedCalendars: true,
        },
      },
      function (start, end, label) {
        startDate = start.startOf("day").format("YYYY-MM-DD");
        endDate = end.endOf("day").format("YYYY-MM-DD");
      }
    );
  });
}

function convertToChartDepSecData(jsonData) {
  // Initialize the chart data structure
  let chartDepSecData = {
    labels: [], // For labels (x-axis)
    datasets: [], // For datasets (y-axis)
  };

  // Extract unique titles for labels
  const labels = [...new Set(jsonData.map((item) => item.title))];
  chartDepSecData.labels = labels;

  // Create a map to hold datasets by depsec_name
  const sectionDataMap = {};

  jsonData.forEach((item) => {
    if (!sectionDataMap[item.depsec_name]) {
      sectionDataMap[item.depsec_name] = [];
    }
    sectionDataMap[item.depsec_name].push({
      title: item.title,
      count: parseInt(item.COUNT, 10), // Convert COUNT to number
    });
  });

  // Create datasets for each section name
  for (const [sectionName, data] of Object.entries(sectionDataMap)) {
    const dataset = {
      label: sectionName,
      data: labels.map((label) => {
        const item = data.find((d) => d.title === label);
        return item ? item.count : 0;
      }),
      backgroundColor: getRandomColor(), // Function to generate random colors
      borderColor: getRandomColor(),
      borderWidth: 1,
    };

    chartDepSecData.datasets.push(dataset);
  }

  return chartDepSecData;
}

function convertToChartTotalDepSecData(jsonData) {
  let chartToData = {
    labels: [], // For depsec_name (x-axis)
    datasets: [
      {
        // Single dataset for aggregated counts
        label: "จำนวนครั้งที่พบ",
        data: [], // Counts corresponding to depsec_name
        backgroundColor: [], // Array for colors
        borderColor: [], // Array for border colors
        borderWidth: 1,
      },
    ],
  };

  // Aggregate counts by depsec_name
  const countMap = {};

  jsonData.forEach((el) => {
    if (!countMap[el.depsec_name]) {
      countMap[el.depsec_name] = 0;
    }
    countMap[el.depsec_name] += parseInt(el.COUNT, 10); // Aggregate counts
  });

  // Set labels and data
  chartToData.labels = Object.keys(countMap);
  chartToData.datasets[0].data = Object.values(countMap);

  // Generate unique colors for each bar
  chartToData.datasets[0].backgroundColor = chartToData.labels.map(() => getRandomColor());
  chartToData.datasets[0].borderColor = chartToData.datasets[0].backgroundColor;

  return chartToData;
}

//on doc ready
$(document).ready(function () {
  initializeDateRange();
  initializeReportChart();
});

$("#daterange").bind("change", function () {
  updateChartByDateChange();
  updateChartByDepSecChange();
  updateChartByTotalDepSecChange();
});

$("#par_depsec").bind("change", function () {
  updateChartByDepSecChange();
});
