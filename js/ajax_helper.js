async function getAsync(url) {
  let exportData = { message: "blank" };
  try {
    await $.get(url).done(async (data, status, xhr) => {
      exportData = JSON.parse(data);
    });
  } catch (ex) {
    const res = JSON.parse(ex.responseText);
    exportData = {
      message: res.message,
      code: res.code,
      codeMessage: res.status,
    };
  }
  return exportData;
}

async function postAsync(url, data) {
  let exportData = { message: "blank" };
  try {
    await $.post(url, JSON.stringify(data)).done(async (data, status, xhr) => {
      exportData = await JSON.parse(data);
    });
  } catch (ex) {
    const res = JSON.parse(ex.responseText);
    exportData = {
      message: res.message,
      code: res.code,
      codeMessage: res.status,
    };
  }
  return exportData;
}

async function postFormDataAsync(url, formData) {
  let exportData = { message: "blank" };
  console.log(formDataToJson(formData));
  try {
    await $.ajax({
      url: url,
      type: "POST",
      data: formData,
      dataType: "json",
      contentType: false,
      processData: false,
    }).done(async (data, status, xhr) => {
      exportData = data;
    });
  } catch (ex) {
    const res = JSON.parse(ex.responseText);
    exportData = {
      message: res.message,
      code: res.code,
      codeMessage: res.status,
    };
  }
  return exportData;
}

function formDataToJson(formData) {
  var json = {};
  formData.forEach(function (value, key) {
    if (json[key]) {
      json[key] += "," + value;
    } else {
      json[key] = value;
    }
  });
  return json;
}

async function md5(data) {
  var hash = await CryptoJS.MD5(data);
  return hash.toString();
}
