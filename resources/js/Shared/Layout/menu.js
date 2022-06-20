export const menuItems = [{
        id: 1,
        label: "Menu",
        isTitle: true,
        user: [],
    },
    {
        id: 2,
        label: "Home",
        icon: "bx-home-circle",
        link: "/home",
        component: "Home/Index",
        user: [],
    },
    {
        id: 3,
        label: "Users",
        icon: "bxs-user-circle",
        link: "/users",
        component: "Users/Index",
        name: "Users",
        user: ['Super Administrator'],
    },
];