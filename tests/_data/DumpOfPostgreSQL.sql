DROP TABLE IF EXISTS public.translations;
CREATE TABLE public.translations (
    id serial PRIMARY KEY,
    key character varying(255) NOT NULL,
    language character(2) NOT NULL,
    region character varying(255),
    value text NOT NULL
);

ALTER TABLE public.translations OWNER TO postgres;