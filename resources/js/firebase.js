// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getMessaging } from "firebase/messaging";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyAiElkmNSl0K-N0Rz4kuqKAXrr6Eg7oo64",
    authDomain: "fyptestv2-37c45.firebaseapp.com",
    databaseURL: "https://fyptestv2-37c45-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "fyptestv2-37c45",
    storageBucket: "fyptestv2-37c45.firebasestorage.app",
    messagingSenderId: "500961952253",
    appId: "1:500961952253:web:a846193490974d3667d994"
  };

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

export { messaging }; 