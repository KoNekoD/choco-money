export const userSidebarRoutes = [
    {title: 'Вход', url: '/login', role: 'ROLE_UNAUTHORIZED'},
    {title: 'Регистрация', url: '/register', role: 'ROLE_UNAUTHORIZED'},
    {title: 'Мой аккаунт', url: '/me', role: 'ROLE_AUTHORIZED'},
    {title: 'Выход', url: '/logout', role: 'ROLE_AUTHORIZED'},
];
