import Alert from "./alert";

export default {
  fetchUserList: async (query, successCallback = () => {}, alwaysCallBack = () => {}) => {
    $.get(`/api/users?${query}`, successCallback)
      .always(alwaysCallBack)
  },

  fetchUserPosts: async (id, successCallback = () => {}, alwaysCallBack = () => {}) => {
    $.get(`/api/posts/${id}`, successCallback)
      .always(alwaysCallBack)
  },

  createUser: async (data, successCallback = () => {}, errorCallback = () => {}) => {
    $.ajax({
      url: `/api/users/create`,
      type: 'POST',
      dataType: 'json',
      contentType: 'application/json; charset=utf-8',
      data: JSON.stringify(data),
      success: successCallback,
      error: errorCallback
    });
  },

  updateUser: async (id, data, successCallback = () => {}, errorCallback = () => {}) => {
    $.ajax({
      url: `/api/users/${id}`,
      type: 'PUT',
      dataType: 'json',
      contentType: 'application/json; charset=utf-8',
      data: JSON.stringify(data),
      success: successCallback,
      error: errorCallback
    });
  }
}