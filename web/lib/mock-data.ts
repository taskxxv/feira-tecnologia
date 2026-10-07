export type Profile = { id: number; name: string; handle: string; role: string; accent: string };
export type SocialPost = {
  id: number;
  author: Profile;
  text: string;
  topic: string;
  createdAt: string;
  likes: number;
  reposts: number;
  comments: number;
  liked?: boolean;
  reposted?: boolean;
  commentsList: { id: number; author: Profile; text: string }[];
};

export const me: Profile = { id: 99, name: "Marina Alves", handle: "marina.alves", role: "Estudante curiosa", accent: "#7ce2c0" };
export const profiles: Profile[] = [
  me,
  { id: 1, name: "Caio Nogueira", handle: "caio.n", role: "Professor de História", accent: "#ff9c86" },
  { id: 2, name: "Bia Campos", handle: "biacampos", role: "Vestibulanda", accent: "#a99cff" },
  { id: 3, name: "Ravi Martins", handle: "ravi.m", role: "Clube de Ciências", accent: "#f5c76a" },
];

export const initialPosts: SocialPost[] = [
  {
    id: 1, author: profiles[1], topic: "História", createdAt: "há 18 min",
    text: "A memória de uma cidade também mora nos detalhes: no nome da rua, na receita da avó, no sotaque que muda de bairro para bairro. Que história pequena da sua comunidade merece ser contada?",
    likes: 42, reposts: 8, comments: 6, commentsList: [
      { id: 11, author: profiles[2], text: "A feira de domingo do meu bairro! Tem uma banca com fotos de 1980." },
    ],
  },
  {
    id: 2, author: profiles[2], topic: "Matemática", createdAt: "há 42 min",
    text: "Descobri um jeito visual de entender funções quadráticas: imaginar a parábola como uma ponte e o vértice como o ponto de maior ou menor altura. Alguém quer trocar exercícios?",
    likes: 31, reposts: 4, comments: 3, commentsList: [],
  },
  {
    id: 3, author: profiles[3], topic: "Ciência", createdAt: "ontem",
    text: "Nosso experimento com sementes mostrou como a luz muda o crescimento, mas a conversa mais interessante foi sobre o que a gente chama de evidência. Ciência também é aprender a fazer perguntas melhores.",
    likes: 58, reposts: 12, comments: 9, commentsList: [],
  },
];

export const initialReports = [
  { id: 1, category: "Convivência", severity: "moderada", status: "nova", text: "Seria importante ter mais sombra no pátio durante o intervalo." },
  { id: 2, category: "Acessibilidade", severity: "alta", status: "lida", text: "A sinalização da biblioteca ainda não está acessível para todos." },
];
