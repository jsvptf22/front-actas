import UserAutocomplete from "./components/UserAutocomplete.vue";
import Subject from "./components/Subject.vue";
import Topics from "./components/Topics.vue";
import Topic from "./components/Topic.vue";
import Roles from "./components/Roles.vue";

const PDocumentBuilderRoutes = [
    {
        path: "/users",
        component: UserAutocomplete
    },
    {
        path: "/subject",
        component: Subject
    },
    {
        path: "/topics",
        component: Topics
    },
    {
        path: "/topic",
        component: Topic
    },
    {
        path: "/roles",
        component: Roles
    }
];

export default PDocumentBuilderRoutes;
