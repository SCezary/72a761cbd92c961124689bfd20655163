import Alert from './alert';
import Form from './form';
import Api from './api';

const userListItemTemplate = $('#user-row-template');
const userPostTemplate = $('#user-post-template');
let selectedUser = null;
const form = new Form('form#user-form');

const updateUserList = (responseData) => {
  const data = responseData?.data || [];
  const userTableBodyComponent = $('#user-table-body');
  userTableBodyComponent.html(''); // clear table
  if (data?.length) {
    data.forEach(user => {
      const itemTemplate = userListItemTemplate.clone().removeClass('hidden').attr('id', user.id);
      itemTemplate.find('#user-id').html(user?.id ?? '#')
      itemTemplate.find('#user-name').html(user?.name ?? '-')
      itemTemplate.find('#user-email').html(user?.email ?? '-')
      itemTemplate.find('#user-gender').html(user?.gender ?? '-')
      itemTemplate.find('#user-status').html(user?.status ?? '')
      userTableBodyComponent.append(itemTemplate);
    })
  }
}

const updateUserPostsList = (responseData) => {
  const data = responseData?.data || [];
  const postsListComponent = $('#user-posts-list');
  postsListComponent.html('') // clear user posts list
  if (data?.length) {
    data.forEach(post => {
      const itemTemplate = userPostTemplate.clone().removeClass('hidden').attr('id', post.id);
      itemTemplate.find('#user-title').html(post?.title ?? '#')
      itemTemplate.find('#user-body').html(post?.body ?? '-')
      postsListComponent.append(itemTemplate);
    })
  } else {
    postsListComponent.append('No posts for selected user');
  }

  if (selectedUser !== null) {
    const selectedUserRow = $('#user-table-body').find(`tr#${selectedUser}`);
    if (selectedUserRow?.length) {
      const userName = selectedUserRow.find('#user-name');
      $('#selected-user').html(`<b>${userName.html() || ''}</b> Posts <a href="/update/${selectedUser}" target="_blank">Edit User</a>`)
    } else {
      $('#selected-user').html(`Selected User Posts <a href="/update/${selectedUser}" target="_blank">Edit User</a>`)
    }
  }
}

const fetchUserList = async (resetPage = false) => {
  showSpinner();

  const query = buildQuery(resetPage);
  void Api.fetchUserList(query, updateUserList, () => {
    hideSpinner();
  })
}

const fetchUserPosts = async (userId) => {
  showSpinner();
  void Api.fetchUserPosts(userId, updateUserPostsList, () => {
    hideSpinner();
  })
}

const createUser = async (data) => {
  form.clearFormErrors();
  void Api.createUser(data, () => {
    Alert.showSuccess('User created successfully!')
  }, (response) => {
    const responseData = response.responseJSON;
    if (Object.keys(responseData?.errors || {})?.length > 0) {
      form.handleFormErrors(responseData.errors);
    } else {
      Alert.showError(responseData?.message || 'User could not be created, try again later.')
    }
  })
};

const updateUser = async (id, data) => {
  form.clearFormErrors();
  void Api.updateUser(id, data, () => {
    Alert.showSuccess('User updated successfully!')
  }, (response) => {
    const responseData = response.responseJSON;
    if (Object.keys(responseData?.errors || {})?.length > 0) {
      form.handleFormErrors(responseData.errors);
    } else {
      Alert.showError(responseData?.message || 'User could not be updated, try again later.')
    }
  })
}

const buildQuery = (resetPage = false) => {
  let query = [];

  let perPage = 10;
  let page = 1;

  if (!resetPage) {
    perPage = parseInt($('select#user-table-page :selected')?.val() || 10);
    page = $('#current-page').attr('data-page');

    if (perPage > 100) {
      perPage = 100;
    } else if (perPage < 10) {
      perPage = 10
    }
  }


  const search = ($('#search-input')?.val() || '').trim();

  query.push(`page=${page}`);
  query.push(`per-page=${perPage}`);
  query.push(`search=${search}`);

  return query.join('&');
}

const showSpinner = () => {
  const spinnerComponent = $('#table-spinner');
  spinnerComponent.removeClass('hidden');
}

const hideSpinner = () => {
  const spinnerComponent = $('#table-spinner');
  if (!spinnerComponent.hasClass('hidden')) {
    spinnerComponent.addClass('hidden');
  }
}

const updatePage = (page) => {
  const currentPageComponent = $('#current-page');
  currentPageComponent.attr('data-page', page);
  currentPageComponent.html(`Page: ${page}`);
}

$(function() {
  void fetchUserList();

  $('select#user-table-page').on('change', function (e) {
    e.preventDefault();
    updatePage(1);

    void fetchUserList();
  })

  $('#prev-page-button').on('click', function (e) {
    e.preventDefault();
    const currentPageComponent = $('#current-page');

    const currentPage = parseInt(currentPageComponent.attr('data-page'));
    const newPage = currentPage <= 1 ? 1 : currentPage - 1
    updatePage(newPage)

    void fetchUserList();
  })

  $('#next-page-button').on('click', function (e) {
    e.preventDefault();
    const currentPageComponent = $('#current-page');

    const currentPage = parseInt(currentPageComponent.attr('data-page'));
    const newPage = currentPage + 1;
    updatePage(newPage);

    void fetchUserList();
  })

  $('#search-button').on('click', function (e) {
    e.preventDefault();
    updatePage(1);

    void fetchUserList(true);
  })

  $('body').on('click', 'tr.user-row', function (e) {
    const userId = $(this).attr('id');
    selectedUser = userId;

    void fetchUserPosts(userId);
  })

  $('form#user-form').on('submit', function (e) {
    e.preventDefault();
    const formData = form.getFormData();
    const userFormId = $('input#user-form-id');

    if (userFormId?.length && userFormId.val() !== '') {
      void updateUser(userFormId.val(), formData);
    } else {
      void createUser(formData);
    }
  });
});